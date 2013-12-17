<?php
namespace libraries\communicate;

use models\UnreadMessageModel;
use models\MessageModel;
use models\UserModel;
use models\UserModelForRegistration;
use models\ProjectModel;
use models\ProjectSettingsModel;
use libraries\sms\SmsModel;
use libraries\sms\SmsQueue;

class CommunicateDelivery implements IDelivery
{
	/**
	 * (non-PHPdoc)
	 * @see libraries\communicate.IDelivery::sendEmail()
	 */
	public function sendEmail($from, $to, $subject, $content) {
		Email::send($from, $to, $subject, $content);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see libraries\communicate.IDelivery::sendSms()
	 */
	public function sendSms($smsModel) {
		SmsQueue::queue($smsModel);
	}
	
}

class CommunicateHelper
{

	/**
	 *
	 * @param string $fileName
	 * @return \Twig_Template
	 */
	public static function templateFromFile($fileName) {
		if (defined('TestMode')) {
			$options = array();
		} else {
			$options = array(
					'cache' => APPPATH . '/cache',
			);
		}
		$loader = new \Twig_Loader_Filesystem(APPPATH . '/views');
		$twig = new \Twig_Environment($loader, $options);
		$template = $twig->loadTemplate($fileName);
		return $template;
	}

	/**
	 *
	 * @param string $fileName
	 * @return \Twig_Template
	 */
	public static function templateFromString($templateCode) {
		if (defined('TestMode')) {
			$options = array();
		} else {
			$options = array(
					'cache' => APPPATH . '/cache',
			);
		}
		$loader = new \Twig_Loader_String();
		$twig = new \Twig_Environment($loader, $options);
		$template = $twig->loadTemplate($templateCode);
		return $template;
	}

	/**
	 * 
	 * @param SmsModel $smsModel
	 * @param IDelivery $delivery
	 */
	public static function deliverSMS($smsModel, IDelivery $delivery = null) {
		// Create our default delivery mechanism if one is not passed in.
		if ($delivery == null) {
			$delivery = new CommunicateDelivery();
		}
		
		// Deliver the sms message
		$delivery->sendSMS($smsModel);
	}

	/**
	 * 
	 * @param mixed $from
	 * @param mixed $to
	 * @param string $subject
	 * @param string $content
	 * @param IDelivery $delivery
	 */
	public static function deliverEmail($from, $to, $subject, $content, IDelivery $delivery = null) {
		// Create our default delivery mechanism if one is not passed in.
		if ($delivery == null) {
			$delivery = new CommunicateDelivery();
		}

		// Deliver the email message
		$delivery->sendEmail($from, $to, $subject, $content);
	}

}

class Communicate 
{
	/**
	 * 
	 * @param array $users array<UserModel> 
	 * @param ProjectSettingsModel $project
	 * @param string $subject
	 * @param string $smsTemplate
	 * @param string $emailTemplate
	 * @param IDelivery $delivery
	 */
	public static function communicateToUsers($users, $project, $subject, $smsTemplate, $emailTemplate, IDelivery $delivery = null) {
		
		// store message in database
		$messageModel = new MessageModel($project);
		$messageModel->subject = $subject;
		$messageModel->content = $emailTemplate;
		$messageId = $messageModel->write();
		
		
		foreach ($users as $user) {
			self::communicateToUser($user, $project, $subject, $smsTemplate, $emailTemplate, $delivery);
			$unreadModel = new UnreadMessageModel($user->id->asString(), $project->id->asString());
			$unreadModel->markUnread($messageId);
			$unreadModel->write();
		}
		if (isset($project->smsSettings)) {
			SmsQueue::processQueue($project->databaseName());
		}
		
		return $messageId;
	}
	
	/**
	 * 
	 * @param UserModel $user
	 * @param ProjectSettingsModel $project
	 * @param string $subject
	 * @param string $smsTemplate
	 * @param string $emailTemplate
	 * @param IDelivery $delivery
	 */
	public static function communicateToUser($user, $project, $subject, $smsTemplate, $emailTemplate, IDelivery $delivery = null) {
		$broadcastMessageContent = "";
		
		// Prepare the email message if required
		if ($user->communicate_via == UserModel::COMMUNICATE_VIA_EMAIL || $user->communicate_via == UserModel::COMMUNICATE_VIA_BOTH) {
			$from = array($project->emailSettings->fromAddress => $project->emailSettings->fromName);
			$to = array($user->email => $user->name);
			$vars = array(
					'user' => $user,
					'project' => $project
			);
			$t = CommunicateHelper::templateFromString($emailTemplate);
			$content = $t->render($vars);
			
			$broadcastMessageContent = $content;
		
			CommunicateHelper::deliverEmail($from, $to, $subject, $content, $delivery);
		}
		
		// Prepare the sms message if required
		if (isset($project->smsSettings) && $project->smsSettings->hasValidCredentials()) {
			if ($user->communicate_via == UserModel::COMMUNICATE_VIA_SMS || $user->communicate_via == UserModel::COMMUNICATE_VIA_BOTH) {
				$databaseName = $project->databaseName();
				$sms = new SmsModel($databaseName);
				$sms->providerInfo = $project->smsSettings->accountId . '|' . $project->smsSettings->authToken;
				$sms->to = $user->mobile_phone;
				$sms->from = $project->smsSettings->fromNumber;
				$vars = array(
						'user' => $user,
						'project' => $project
				);
				$t = CommunicateHelper::templateFromString($smsTemplate);
				$sms->message = $t->render($vars);
		
				CommunicateHelper::deliverSMS($sms, $delivery);
			}
		}
	}
	
	/**
	 * Send an email to validate a user when they sign up.
	 * @param UserModelBase $userModel
	 * @param ProjectModel $projectModel
	 * @param IDelivery $delivery
	 */
	public static function sendSignup($userModel, $projectModel = null, IDelivery $delivery = null) {
		$userModel->setValidation(7);
		$userModel->write();
		if (is_null($projectModel)) { 
			// no project in scope, signup to ScriptureForge only
			$from = array(LF_DEFAULT_EMAIL => LF_DEFAULT_EMAIL_NAME);
			$subject = 'ScriptureForge account validation';
			$vars = array(
					'user' => $userModel,
					'link' => 'http://' . $_SERVER['SERVER_NAME'] . '/validate/' . $userModel->validationKey,
			);
			$t = CommunicateHelper::templateFromFile('email/en/SignupValidate.html');
		} else {
			// project in scope, signup to project on ScriptureForge
			$from = array(LF_DEFAULT_EMAIL => $projectModel->projectName . ' on ' . LF_DEFAULT_EMAIL_NAME);
			$subject = $projectModel->projectName . ' project on ScriptureForge account validation';
			$vars = array(
					'user' => $userModel,
					'project' => $projectModel,
					'link' => 'http://' . $_SERVER['SERVER_NAME'] . '/validate/' . $userModel->validationKey,
			);
			$t = CommunicateHelper::templateFromFile('email/en/SignupWithProjectValidate.html');
		}
		$html = $t->render($vars);

		CommunicateHelper::deliverEmail(
			$from,
			array($userModel->emailPending => $userModel->name),
			$subject,
			$html,
			$delivery
		);
	}
	
	/**
	 * 
	 * @param UserModelBase $inviterUserModel
	 * @param UserModelBase $toUserModel
	 * @param ProjectModel $projectModel
	 * @param IDelivery $delivery
	 */
	public static function sendInvite($inviterUserModel, $toUserModel, $projectModel, IDelivery $delivery = null) {
		$toUserModel->setValidation(7);
		$toUserModel->write();
		$vars = array(
			'user' => $inviterUserModel,
			'project' => $projectModel,
			'link' => 'http://' . $_SERVER['SERVER_NAME'] . '/registration#/?v=' . $toUserModel->validationKey,
		);
		$t = CommunicateHelper::templateFromFile('email/en/InvitationValidate.html');
		$html = $t->render($vars);

		CommunicateHelper::deliverEmail(
			array(LF_DEFAULT_EMAIL => LF_DEFAULT_EMAIL_NAME),
			array($toUserModel->emailPending => $toUserModel->name),
			'ScriptureForge account signup validation',
			$html,
			$delivery
		);
	}
	
	/**
	 * 
	 * @param UserModel $toUserModel
	 * @param string $newUserName
	 * @param string $newUserPassword
	 * @param ProjectModel $projectModel
	 * @param IDelivery $delivery
	 */
	public static function sendNewUserInProject($toUserModel, $newUserName, $newUserPassword, $projectModel, IDelivery $delivery = null) {
		$vars = array(
				'user' => $toUserModel,
				'newUserName' => $newUserName,
				'newUserPassword' => $newUserPassword,
				'project' => $projectModel,
		);
		$t = CommunicateHelper::templateFromFile('email/en/NewUserInProject.html');
		$html = $t->render($vars);
	
		CommunicateHelper::deliverEmail(
			array(LF_DEFAULT_EMAIL => LF_DEFAULT_EMAIL_NAME),
			array($toUserModel->email => $toUserModel->name),
			'ScriptureForge new user login for project',
			$html,
			$delivery
		);
	}
	
}

?>
