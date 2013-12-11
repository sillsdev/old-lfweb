<?php

namespace models\commands;

use libraries\lfdictionary\environment\LexProject;
use libraries\lfdictionary\store\LexStoreType;
use libraries\lfdictionary\store\LexStoreController;
use libraries\lfdictionary\store\LexStore;
use models\ActivityModel;
use models\CommentModel;
use models\lex\LexEntryModel;
use models\mapper\IdReference;
use models\ProjectModel;
use models\ProjectModelFixer;
use models\QuestionModel;
use models\QuestionAnswersListModel;
use models\UserModel;
use models\UnreadActivityModel;
use models\UnreadAnswerModel;

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
		$user = new UserModel($commentModel->userRef->asString());
		$user2 = new UserModel($answer->userRef->asString());
		$activity->action = ($mode == 'update') ? ActivityModel::UPDATE_COMMENT : ActivityModel::ADD_COMMENT;
		$activity->userRef->id = $commentModel->userRef->asString();
		$activity->userRef2->id = $answer->userRef->asString();
		$activity->questionRef->id = $questionId;
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
		$user = new UserModel($answerModel->userRef->asString());
		
		$activity->action = ($mode == "update") ? ActivityModel::UPDATE_ANSWER : ActivityModel::ADD_ANSWER;
		$activity->userRef->id = $answerModel->userRef->asString();
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::QUESTION, $question->title);
		$activity->addContent(ActivityModel::ANSWER, $answerModel->content);
		$activity->addContent(ActivityModel::USER, $user->username);
//		return $activity->write();
		$activityId = $activity->write();
		UnreadActivityModel::markUnreadForProjectMembers($activityId, $projectModel);
		UnreadAnswerModel::markUnreadForProjectMembers($answerModel->id->asString(), $projectModel, $questionId, $answerModel->userRef->asString());
		return $activityId;
	}
	
	public static function addAnswer($projectModel, $questionId, $answerModel) {
		return ActivityCommands::updateAnswer($projectModel, $questionId, $answerModel, 'add');
	}
		
	/**
	 * @param ProjectModel $projectModel
	 * @param string $questionId
	 * @param QuestionModel $questionModel
	 * @return string activity id
	 */
	public static function addQuestion($projectModel, $questionId, $questionModel) {
		$activity = new ActivityModel($projectModel);
		$activity->action = ActivityModel::ADD_QUESTION;
		$activity->questionRef->id = $questionId;
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
		$answer = $question->answers[$answerId];
		$user = new UserModel($userId);
		$user2 = new UserModel($answer->userRef->asString());
		$activity = new ActivityModel($projectModel);
		$activity->action = ($mode == 'increase') ? ActivityModel::INCREASE_SCORE : ActivityModel::DECREASE_SCORE;
		$activity->userRef->id = $userId;
		$activity->questionRef->id = $questionId;
		$activity->addContent(ActivityModel::QUESTION, $question->title);
		$activity->addContent(ActivityModel::ANSWER, $answer->content);
		$activity->addContent(ActivityModel::USER, $user->username);
		$activity->addContent(ActivityModel::USER, $user2->username);
		return $activity->write();
	}
	
	/**
	 * @param ProjectModel $projectModel
	 * @param string $userId
	 * @param LexEntryModel $entry
	 * @param Action $action
	 * @return string activity id
	 */
	public static function writeEntry($projectModel, $userId, $entry, $action) {
		$activity = new ActivityModel($projectModel);
		$activity->userRef->id = $userId;
		if($action == 'update'){
			$activity->action = ActivityModel::UPDATE_ENTRY;
		} else {
			$activity->action = ActivityModel::ADD_ENTRY;
		}
		
		$activity->addContent(ActivityModel::ENTRY, $entry);
		return $activity->write();
	}
	
	/**
	 * @param ProjectModel $projectModel
	 * @param string $userId
	 * @param string entry id
	 * @return string activity id
	 */
	public static function deleteEntry($projectModel, $userId, $id) {
		$activity = new ActivityModel($projectModel);
		$activity->userRef->id = $userId;
		$activity->action = ActivityModel::DELETE_ENTRY;
	
		$entry = self::getEntry($projectModel->id->asString(), $id);
		$activity->addContent(ActivityModel::ENTRY, $entry->lexeme[$projectModel->languageCode]);
		return $activity->write();
	}
	
	/**
	 * @param string $projectId
	 * @param string entry id
	 * @return LexEntryModel
	 */
	public static function getEntry($projectId, $id) {
		$project = new ProjectModel($projectId);
		ProjectModelFixer::ensureVLatest($project);
		
		$entry = new LexEntryModel($project, $id);
		return $entry;
	}
	
}

?>
