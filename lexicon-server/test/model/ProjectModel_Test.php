<?php
use models\rights\Operation;
use models\rights\Domain;
use models\rights\Roles;
use models\mapper\Id;
use models\mapper\MongoStore;
use models\UserModel;
use models\ProjectModel;

require_once(dirname(__FILE__) . '/../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once(TEST_PATH . 'common/MongoTestEnvironment.php');

require_once(SourcePath . "models/UserModel.php");
require_once(SourcePath . "models/ProjectModel.php");

class TestProjectModel extends UnitTestCase {

	private $_someProjectId;
	
	function __construct() {
		$e = new MongoTestEnvironment();
		$e->clean();
	}
	
	function testWrite_ReadBackSame() {
		$model = new ProjectModel();
		$model->languageCode = "SomeLanguage";
		$model->projectname = "SomeProject";
		//$model->users->refs = array('1234');
		$id = $model->write();
		$this->assertNotNull($id);
		$this->assertIsA($id, 'string');
		$this->assertEqual($id, $model->id->asString());
		$otherModel = new ProjectModel($id);
		$this->assertEqual($id, $otherModel->id->asString());
		$this->assertEqual('SomeLanguage', $otherModel->languageCode);
		$this->assertEqual('SomeProject', $otherModel->projectname);
		//$this->assertEqual(array('1234'), $otherModel->users->refs);
		
		$this->_someProjectId = $id;
	}

	function testProjectList_HasCountAndEntries()
	{
		$model = new models\ProjectListModel();
		$model->read();
		
		$this->assertNotEqual(0, $model->count);
		$this->assertNotNull($model->entries);
	}
	
	function testProjectAddUser_ExistingProject_ReadBackAdded() {
		$e = new MongoTestEnvironment();
		
		// setup user and projects
		$userId = $e->createUser('jsmith', 'joe smith', 'joe@email.com');
		$userModel = new UserModel($userId);
		$projectModel = $e->createProject('new project');
		$projectId = $projectModel->id->asString();
		
		// create the reference
		$projectModel->addUser($userId, Roles::USER);
		$userModel->addProject($projectId);
		$projectModel->write();
		$userModel->write();
		
		$this->assertTrue($projectModel->users->offsetExists($userId), "'$userId' not found in project.");
		$otherProject = new ProjectModel($projectId);
		$this->assertTrue($otherProject->users->offsetExists($userId), "'$userId' not found in other project.");
	}
	
	
	// TODO move Project <--> User operations to a separate ProjectUserCommands tests
	
	function testProjectRemoveUser_ExistingProject_Removed() {
		$e = new MongoTestEnvironment();
		
		// setup user and projects
		$userId = $e->createUser('jsmith', 'joe smith', 'joe@email.com');
		$userModel = new UserModel($userId);
		$projectModel = $e->createProject('new project');
		$projectId = $projectModel->id->asString();

		// create the reference
		$projectModel->addUser($userId, Roles::USER);
		$userModel->addProject($projectId);
		$projectModel->write();
		$userModel->write();
		
		// assert the reference is there		
		$this->assertTrue($projectModel->users->offsetExists($userId), "'$userId' not found in project.");
		$otherProject = new ProjectModel($projectId);
		$this->assertTrue($otherProject->users->offsetExists($userId), "'$userId' not found in other project.");
		
		// remove the reference
		$projectModel->removeUser($userId);
		$userModel->removeProject($projectId);
		$projectModel->write();
		$userModel->write();
		
		// testing
		$this->assertFalse($projectModel->users->offsetExists($userId), "'$userId' not found in project.");
		$otherProject = new ProjectModel($this->_someProjectId);
		$this->assertFalse($otherProject->users->offsetExists($userId), "'$userId' not found in other project.");
		$project = new ProjectModel($this->_someProjectId);
	}
	
	function testProjectAddUser_TwiceToSameProject_AddedOnce() {
		// note I am not testing the reciprocal reference here - 2013-07-09 CJH
		$e = new MongoTestEnvironment();
		
		// setup user and projects
		$userId = $e->createUser('jsmith', 'joe smith', 'joe@email.com');
		$userModel = new UserModel($userId);
		$projectModel = $e->createProject('new project');
		$projectId = $projectModel->id;
		
		$projectModel->addUser($userId, Roles::USER);
		$this->assertEqual(1, $projectModel->users->count());
		$projectModel->addUser($userId, Roles::USER);
		$this->assertEqual(1, $projectModel->users->count());
	}
	
	function testProjectListUsers_TwoUsers_ListHasDetails() {
		$e = new MongoTestEnvironment();
		$userId1 = $e->createUser('user1', 'User One', 'user1@example.com');
		$um1 = new UserModel($userId1);
		$userId2 = $e->createUser('user2', 'User Two', 'user2@example.com');
		$um2 = new UserModel($userId2);
		$project = $e->createProject(LF_TESTPROJECT);
		$projectId = $project->id->asString();
		
		// Check the list users is empty
		$result = $project->listUsers();
		$this->assertEqual(0, $result->count);
		$this->assertEqual(array(), $result->entries);
				
		// Add our two users
		$project->addUser($userId1, Roles::USER);
		$um1->addProject($projectId);
		$um1->write();
		
		$project->addUser($userId2, Roles::USER);
		$um2->addProject($projectId);
		$um2->write();
		$project->write();
		
		$otherProject = new ProjectModel($projectId);
		$result = $otherProject->listUsers();
		$this->assertEqual(2, $result->count);
		$this->assertEqual(
			array(
				array(
		          'email' => 'user1@example.com',
		          'name' => 'User One',
		          'username' => 'user1',
		          'id' => $userId1,
				  'role' => Roles::USER
				), 
				array(
		          'email' => 'user2@example.com',
		          'name' => 'User Two',
		          'username' => 'user2',
		          'id' => $userId2,
				  'role' => Roles::USER
				)
			), $result->entries
		);
		
	}
	
	function testRemove_RemovesProject() {
		$e = new MongoTestEnvironment();
		$project = new ProjectModel($this->_someProjectId);
		
		$this->assertTrue($project->exists($this->_someProjectId));
		
		$project->remove();
		
		$this->assertFalse($project->exists($this->_someProjectId));
	}
	
	function testDatabaseName_Ok() {
		$project = new ProjectModel();
		$project->projectname = 'Some Project';
		$result = $project->databaseName();
		$this->assertEqual('lf_some_project', $result);
	}
	
	function testHasRight_Ok() {
		$userId = MongoTestEnvironment::mockId();
		$project = new ProjectModel();
		$project->addUser($userId, Roles::PROJECT_ADMIN);
		$result = $project->hasRight($userId, Domain::QUESTIONS + Operation::CREATE);
		$this->assertTrue($result);
	}
	
	function testGetRightsArray_Ok() {
		$userId = MongoTestEnvironment::mockId();
		$project = new ProjectModel();
		$project->addUser($userId, Roles::PROJECT_ADMIN);
		$result = $project->getRightsArray($userId);
		$this->assertIsA($result, 'array');
		$this->assertTrue(in_array(Domain::QUESTIONS + Operation::CREATE, $result));
	}

	function testCreateFromDomain_3Projects_MatchingProjectCodeSelected() {
		$e = new MongoTestEnvironment();
		$e->clean();
	
		$project1 = $e->createProject('Project1Name');
		$project1->projectCode = ProjectModel::domainToProjectCode('dev.languageforge.org');
		$project1->write();
		$project2 = $e->createProject('Project2Name');
		$project2->projectCode = ProjectModel::domainToProjectCode('jamaicanpsalms.languageforge.org');
		$project2->write();
		$project3 = $e->createProject('Project3Name');
		$project3->projectCode = ProjectModel::domainToProjectCode('languageforge.local');
		$project3->write();
		$projectDomain = 'jamaicanpsalms.local';
	
		$project = ProjectModel::createFromDomain($projectDomain);
	
		$this->assertEqual($project->projectCode, ProjectModel::domainToProjectCode($projectDomain));
	}
	
}

?>