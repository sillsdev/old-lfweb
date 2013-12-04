<?php


use models\dto\QuestionCommentDto;

use models\dto\ActivityListDto;

use models\UnreadAnswerModel;

use models\AnswerModel;

use models\commands\QuestionCommands;

use models\UnreadQuestionModel;

use models\QuestionModel;

use models\UserModel;

use models\commands\ActivityCommands;

use models\UnreadActivityModel;

use models\rights\Roles;

use models\mapper\MongoStore;
use models\ProjectModel;

require_once(dirname(__FILE__) . '/../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once(TEST_PATH . 'common/MongoTestEnvironment.php');
require_once(TEST_PATH . 'common/MockProjectModel.php');

require_once(SourcePath . "models/ProjectModel.php");

class TestUserUnreadModel extends UnitTestCase {

	function __construct() {
	}
	
	function testUnreadActivityModel_MarkUnreadForProjectMembers_noExistingRead_allMarkedUnread() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$user1 = new UserModel($userId1);
		$user1->addProject($project->id->asString());
		$user1->write();
		
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$user2 = new UserModel($userId2);
		$user2->addProject($project->id->asString());
		$user2->write();
		
		$userId3 = $e->createUser('user3', 'user3', 'user3');
		$user3 = new UserModel($userId3);
		$user3->addProject($project->id->asString());
		$user3->write();
		
		$activityId = ActivityCommands::addUserToProject($project, $userId1);
		
		UnreadActivityModel::markUnreadForProjectMembers($activityId, $project);	
		
		$unreadModel = new UnreadActivityModel($userId1);
		$this->assertTrue($unreadModel->isUnread($activityId));
		
		$unreadModel = new UnreadActivityModel($userId2);
		$this->assertTrue($unreadModel->isUnread($activityId));
		
		$unreadModel = new UnreadActivityModel($userId3);
		$this->assertTrue($unreadModel->isUnread($activityId));
	}
	
	function testUnreadActivityModel_isUnread_markedUnread_true() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$activityId = ActivityCommands::addUserToProject($project, $userId1);
		
		$unreadModel = new UnreadActivityModel($userId1);
		$this->assertFalse($unreadModel->isUnread($activityId));
		$unreadModel->markUnread($activityId);
		$unreadModel->write();
		
		$otherUnreadModel = new UnreadActivityModel($userId1);
		$this->assertTrue($otherUnreadModel->isUnread($activityId));
	}
	
	function testUnreadActivityModel_isUnread_markedRead_false() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$activityId = ActivityCommands::addUserToProject($project, $userId1);
		
		$unreadModel = new UnreadActivityModel($userId1);
		$this->assertFalse($unreadModel->isUnread($activityId));
		$unreadModel->markUnread($activityId);
		$unreadModel->write();
		
		$otherUnreadModel = new UnreadActivityModel($userId1);
		$otherUnreadModel->markRead($activityId);
		$otherUnreadModel->write();
		
		$unreadModel->read();
		$this->assertFalse($unreadModel->isUnread($activityId));
		
	}
	
	function testUnreadActivityModel_markAllRead_unreadItems_noUnreadItems() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$activityId1 = ActivityCommands::addUserToProject($project, $userId1);
		$activityId2 = ActivityCommands::addUserToProject($project, $userId2);
		
		$unreadModel = new UnreadActivityModel($userId1);
		$unreadModel->markUnread($activityId1);
		$unreadModel->markUnread($activityId2);
		$unreadModel->write();
		
		$otherUnreadModel = new UnreadActivityModel($userId1);
		$this->assertTrue($otherUnreadModel->isUnread($activityId1));
		$this->assertTrue($otherUnreadModel->isUnread($activityId2));
		$otherUnreadModel->markAllRead();
		$otherUnreadModel->write();
		
		$unreadModel->read();
		$this->assertFalse($unreadModel->isUnread($activityId1));
		$this->assertFalse($unreadModel->isUnread($activityId2));
	}
	
	function testUnreadActivityModel_unreadItems_itemsAreUnread_listsUnreadItems() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$activityId1 = ActivityCommands::addUserToProject($project, $userId1);
		$activityId2 = ActivityCommands::addUserToProject($project, $userId2);
		
		$unreadModel = new UnreadActivityModel($userId1);
		$unreadModel->markUnread($activityId1);
		$unreadModel->markUnread($activityId2);
		$unreadModel->write();
		
		$otherUnreadModel = new UnreadActivityModel($userId1);
		
		$unreadItems = $otherUnreadModel->unreadItems();
		$this->assertEqual(2, count($unreadItems));
		
		$otherUnreadModel->markRead($activityId1);
		$otherUnreadModel->write();
		
		$unreadModel->read();
		$unreadItems = $unreadModel->unreadItems();
		$this->assertEqual(1, count($unreadItems));
	}
	
	function testUnreadQuestionModel_markAllRead_unreadItems_noUnreadItems() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$q1 = new QuestionModel($project);
		$q1->title = "Question 1";
		$qId1 = $q1->write();
		$q2 = new QuestionModel($project);
		$q2->title = "Question 2";
		$qId2 = $q2->write();
		
		$unreadModel = new UnreadQuestionModel($userId1, $project->id->asString());
		$unreadModel->markUnread($qId1);
		$unreadModel->markUnread($qId2);
		$unreadModel->write();
		
		$otherUnreadModel = new UnreadQuestionModel($userId1, $project->id->asString());
		$this->assertTrue($otherUnreadModel->isUnread($qId1));
		$this->assertTrue($otherUnreadModel->isUnread($qId2));
		$otherUnreadModel->markAllRead();
		$otherUnreadModel->write();
		
		$unreadModel->read();
		$this->assertFalse($unreadModel->isUnread($qId1));
		$this->assertFalse($unreadModel->isUnread($qId2));
	}
	
	function testUnreadAnswerModel_multipleUsers_authorIsNotMarkedUnread() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$projectId = $project->id->asString();
		
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$user1 = new UserModel($userId1);
		$user1->addProject($project->id->asString());
		$user1->write();
		
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$user2 = new UserModel($userId2);
		$user2->addProject($project->id->asString());
		$user2->write();
		
		$userId3 = $e->createUser('user3', 'user3', 'user3');
		$user3 = new UserModel($userId3);
		$user3->addProject($project->id->asString());
		$user3->write();
		
		$answer = array('content' => "test answer", 'id' => '');
		
		$question = new QuestionModel($project);
		$question->title = "test question";
		$questionId = $question->write();
		$answerDto = QuestionCommands::updateAnswer($projectId, $questionId, $answer, $userId1);
		$answer = array_pop($answerDto);
		$answerId = $answer['id'];
		
		// the answer author does NOT get their answer marked as unread
		$unreadModel = new UnreadAnswerModel($userId1, $projectId, $questionId);
		$this->assertFalse($unreadModel->isUnread($answerId));
		
		$unreadModel = new UnreadAnswerModel($userId2, $projectId, $questionId);
		$this->assertTrue($unreadModel->isUnread($answerId));
		$unreadModel = new UnreadAnswerModel($userId3, $projectId, $questionId);
		$this->assertTrue($unreadModel->isUnread($answerId));
	}
	
	function testMultipleUnreadModels_multipleUsers_multipleUpdates_multipleVisitsToQuestionPage_usersHaveDifferentUnreadStates() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject("unread_test");
		$projectId = $project->id->asString();
		
		$userId1 = $e->createUser('user1', 'user1', 'user1');
		$user1 = new UserModel($userId1);
		$user1->addProject($project->id->asString());
		$user1->write();
		
		$userId2 = $e->createUser('user2', 'user2', 'user2');
		$user2 = new UserModel($userId2);
		$user2->addProject($project->id->asString());
		$user2->write();
		
		$userId3 = $e->createUser('user3', 'user3', 'user3');
		$user3 = new UserModel($userId3);
		$user3->addProject($project->id->asString());
		$user3->write();
		
		$answer1 = array('content' => "test answer 1", 'id' => '');
		$answer2 = array('content' => "test answer 2", 'id' => '');
		
		$question = new QuestionModel($project);
		$question->title = "test question";
		$questionId = $question->write();
		$answer1Dto = QuestionCommands::updateAnswer($projectId, $questionId, $answer1, $userId1);
		$answer2Dto = QuestionCommands::updateAnswer($projectId, $questionId, $answer2, $userId2);
		$answer1 = array_pop($answer1Dto);
		$answer1Id = $answer1['id'];
		$answer2 = array_pop($answer2Dto);
		$answer2Id = $answer2['id'];
		
		// the answer author does NOT get their answer marked as unread
		$unreadModel = new UnreadAnswerModel($userId1, $projectId, $questionId);
		$this->assertEqual(1, count($unreadModel->unreadItems()));
		
		// the answer author does NOT get their answer marked as unread
		$unreadModel = new UnreadAnswerModel($userId2, $projectId, $questionId);
		$this->assertEqual(1, count($unreadModel->unreadItems()));
		
		$unreadModel = new UnreadAnswerModel($userId3, $projectId, $questionId);
		$this->assertEqual(2, count($unreadModel->unreadItems()));
		
		// user1 visits question page
		$pageDto = QuestionCommentDto::encode($projectId, $questionId, $userId1);
		$unreadModel = new UnreadAnswerModel($userId1, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId2, $projectId, $questionId);
		$this->assertEqual(1, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId3, $projectId, $questionId);
		$this->assertEqual(2, count($unreadModel->unreadItems()));
		
		// user2 visits question page
		$pageDto = QuestionCommentDto::encode($projectId, $questionId, $userId2);
		$unreadModel = new UnreadAnswerModel($userId1, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId2, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId3, $projectId, $questionId);
		$this->assertEqual(2, count($unreadModel->unreadItems()));
		
		// user2 visits question page
		$pageDto = QuestionCommentDto::encode($projectId, $questionId, $userId3);
		$unreadModel = new UnreadAnswerModel($userId1, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId2, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
		$unreadModel = new UnreadAnswerModel($userId3, $projectId, $questionId);
		$this->assertEqual(0, count($unreadModel->unreadItems()));
	}
}

?>
