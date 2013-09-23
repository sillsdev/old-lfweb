<?php

namespace models\commands;

use models\TextModel;

use models\mapper\IdReference;

use models\ActivityModel;

use models\CommentModel;

use models\ProjectModel;
use models\QuestionModel;
use models\UserModel;
use libraries\lfdictionary\store\LexStoreType;
use libraries\lfdictionary\store\LexStoreController;

use models\QuestionAnswersListModel;


use libraries\lfdictionary\store\LexStore;
use libraries\lfdictionary\environment\LexProject;
use models\ProjectModelFixer;

class ActivityCommands
{
	
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param string $questionId
	 * @param string $answerId
	 * @param CommentModel $commentModel
	 * @return string activity id
	 */
	public static function updateComment($projectModel, $questionId, $answerId, $commentModel, $mode = "update") {
		$activity = new ActivityModel($projectModel);
		$question = new QuestionModel($projectModel, $questionId);
		$answer = $question->readAnswer($answerId);
		$text = new TextModel($projectModel, $question->textRef->asString());
		$user = new UserModel($commentModel->userRef->asString());
		$user2 = new UserModel($answer->userRef->asString());
		$activity->action = ($mode == 'update') ? ActivityModel::UPDATE_COMMENT : ActivityModel::ADD_COMMENT;
		$activity->userRef->id = $commentModel->userRef->asString();
		$activity->userRef2->id = $answer->userRef->asString();
		$activity->textRef->id = $text->id->asString();
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::TEXT, $text->title);
		$activity->addContent(ActivityModel::QUESTION, $question->title);
		$activity->addContent(ActivityModel::ANSWER, $answer->content);
		$activity->addContent(ActivityModel::COMMENT, $commentModel->content);
		$activity->addContent(ActivityModel::USER, $user->username);
		$activity->addContent(ActivityModel::USER2, $user2->username);
		return $activity->write();
	}
	
	public static function addComment($projectModel, $questionId, $answerId, $commentModel) {
		return ActivityCommands::updateComment($projectModel, $questionId, $answerId, $commentModel, "add");
	}
	
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param string $questionId
	 * @param string $answerId
	 * @param AnswerModel $answerModel
	 * @return string activity id
	 */
	public static function updateAnswer($projectModel, $questionId, $answerModel, $mode = "update") {
		$activity = new ActivityModel($projectModel);
		$question = new QuestionModel($projectModel, $questionId);
		$text = new TextModel($projectModel, $question->textRef->asString());
		$user = new UserModel($answerModel->userRef->asString());
		
		$activity->action = ($mode == "update") ? ActivityModel::UPDATE_ANSWER : ActivityModel::ADD_ANSWER;
		$activity->userRef->id = $answerModel->userRef->asString();
		$activity->textRef->id = $text->id->asString();
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::TEXT, $text->title);
		$activity->addContent(ActivityModel::QUESTION, $question->title);
		$activity->addContent(ActivityModel::ANSWER, $answerModel->content);
		$activity->addContent(ActivityModel::USER, $user->username);
		return $activity->write();
	}
	
	public static function addAnswer($projectModel, $questionId, $answerModel) {
		return ActivityCommands::updateAnswer($projectModel, $questionId, $answerModel, 'add');
	}
	
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param TextModel $textModel
	 * @return string activity id
	 */
	public static function addText($projectModel, $textId, $textModel) {
		$activity = new ActivityModel($projectModel);
		$activity->action = ActivityModel::ADD_TEXT;
		$activity->textRef->id = $textId;
		$activity->addContent(ActivityModel::TEXT, $textModel->title);
		return $activity->write();
	}
		
	/**
	 * @param ProjectModel $projectModel
	 * @param string $questionId
	 * @param QuestionModel $questionModel
	 * @return string activity id
	 */
	public static function addQuestion($projectModel, $questionId, $questionModel) {
		$activity = new ActivityModel($projectModel);
		$text = new TextModel($projectModel, $questionModel->textRef->asString());
		$activity->action = ActivityModel::ADD_QUESTION;
		$activity->textRef->id = $questionModel->textRef->asString();
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::TEXT, $text->title);
		$activity->addContent(ActivityModel::QUESTION, $questionModel->title);
		return $activity->write();
	}
	
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param string $userId
	 * @return string activity id
	 */
	public static function addUserToProject($projectModel, $userId) {
		$activity = new ActivityModel($projectModel);
		$activity->action = ActivityModel::ADD_USER_TO_PROJECT;
		$activity->userRef->id = $userId; // we can use the userRef in this case because we don't keep track of the user that performed this action
		$user = new UserModel($userId);
		$activity->addContent(ActivityModel::USER, $user->username);
		return $activity->write();
	}
	
	// this may only be useful to log this activity for answers on which the user has commented on or has answered him/herself
	// TODO: how do we implement this?
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param string $questionId
	 * @param string $answerId
	 * @param string $userId
	 * @param string $mode
	 * @return string activity id
	 */
	public static function updateScore($projectModel, $questionId, $answerId, $userId, $mode = 'increase') {
		$activity = new ActivityModel($projectModel);
		$question = new QuestionModel($projectModel, $questionId);
		$text = new TextModel($projectModel, $question->textRef->asString());
		$answer = $question->answers->data[$answerId];
		$user = new UserModel($userId);
		$user2 = new UserModel($answer->userRef->asString());
		$activity = new ActivityModel($projectModel);
		$activity->action = ($mode == 'increase') ? ActivityModel::INCREASE_SCORE : ActivityModel::DECREASE_SCORE;
		$activity->userRef->id = $userId;
		$activity->textRef->id = $text->id->asString();
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::TEXT, $text->title);
		$activity->addContent(ActivityModel::QUESTION, $question->title);
		$activity->addContent(ActivityModel::ANSWER, $answer->content);
		$activity->addContent(ActivityModel::USER, $user->username);
		$activity->addContent(ActivityModel::USER, $user2->username);
		return $activity->write();
	}
	
	/**
	 * 
	 * @param ProjectModel $projectModel
	 * @param Entry $entry
	 * @param Action $action
	 * @return string activity id
	 */
	public static function writeEntry($projectModel, $userId, $entryDto, $action) {
	
		
		$activity = new ActivityModel($projectModel);
		$activity->userRef->id = $userId;
		if($action == 'update'){
			$activity->action = ActivityModel::UPDATE_ENTRY;
		} else {
			$activity->action = ActivityModel::ADD_ENTRY;
		}
		$entry = $entryDto->getEntry();
		$multitex = $entry[$projectModel->languageCode];
		throw new \Exception ('entry'.$multitex);
		$activity->addContent(ActivityModel::ENTRY, $entryDto->getEntry());
		return $activity->write();
	}
	
	/**
	 *
	 * @param ProjectModel $projectModel
	 * @param Guid $guid
	 * @return string activity id
	 */
	public static function deleteEntry($projectModel, $userId, $guid) {
	
		
		$activity = new ActivityModel($projectModel);
		$activity->userRef->id = $userId;
		$activity->action = ActivityModel::DELETE_ENTRY;
	
		$entryModel = self::getEntry($projectModel->id->asString(), $guid);	
		$activity->addContent(ActivityModel::ENTRY, $entryModel['entry'][$projectModel->languageCode]);
		return $activity->write();
	}
	
	public static function getEntry($projectId, $entryGuid) {
		//throw new \Exception ('projectId ' .$projectId ." entryGuid ".$entryGuid);
		$projectModel = new ProjectModel ( $projectId );
		ProjectModelFixer::ensureVLatest ( $projectModel );
	
		$lexProject = new LexProject ( $projectModel );
	
		$store = new LexStoreController ( LexStoreType::STORE_MONGO, $projectModel->databaseName (), $lexProject );
		$result = $store->readEntry ( $entryGuid );
	
		// Sense Level
		foreach ( $result->_senses as $sense ) {
	
			if (! (isset ( $sense->_id ) && strlen ( trim ( $sense->_id ) ) > 0)) {
				$sense->_id = \libraries\lfdictionary\common\UUIDGenerate::uuid_generate_php ();
			}
			// Example Level
			foreach ( $sense->_examples as $example ) {
				if (! (isset ( $example->_id ) && strlen ( trim ( $example->_id ) ) > 0)) {
					$example->_id = \libraries\lfdictionary\common\UUIDGenerate::uuid_generate_php ();
				}
			}
		}
		return $result->encode ();
	}
}

?>