<?php

use models\ProjectModel;

require_once(TEST_PATH . 'common/MockProjectModel.php');

class MongoTestEnvironment
{
	
	/**
	 * @var MongoDB
	 */
	private $_db;
	
	public function __construct() {
		$this->_db = \models\mapper\MongoStore::connect(LF_DATABASE);
	}

	/**
	 * Removes all the collections from the mongo database.
	 * Hopefully this is only ever called on the scriptureforge_test database.
	 */
	public function clean() {
		foreach ($this->_db->listCollections() as $collection)
		{
			$collection->drop();
		}
	}

	/**
	 * Querys the given $collection and returns a MongoCursor.
	 * @param string $collection
	 * @param array $query
	 * @param array $fields
	 * @return MongoCursor
	 */
	public function find($collection, $query, $fields = array()) {
		$collection = $this->_db->$collection;
		return $collection->find($query, $fields);
	}
	
	/**
	 * Writes a user to the users collection.
	 * @param string $username
	 * @param string $name
	 * @param string $email
	 * @return string id
	 */
	public function createUser($username, $name, $email) {
		$userModel = new models\UserModel();
		$userModel->username = $username;
		$userModel->name = $name;
		$userModel->email = $email;
		$userModel->avatar_ref = $username . ".png";
		return $userModel->write();
	}
	
	/**
	 * Writes a project to the projects collection.
	 * @param string $name
	 * @return ProjectModel
	 */
	public function createProject($projectName, $languageCode = 'qaa', $projectType = ProjectModel::PROJECT_LIFT) {
		$projectModel = ProjectModel::create($projectName, $languageCode, $projectType);
		$this->cleanProjectEnvironment($projectModel);
		$projectModel->write();
		return $projectModel;
	}
	
	public function createProjectSettings($name) {
		$projectModel = new models\ProjectSettingsModel();
		$projectModel->projectName = $name;
		$this->cleanProjectEnvironment($projectModel);
		$projectModel->write();
		return $projectModel;
	}
	
	private function cleanProjectEnvironment($projectModel) {
		// clean out old db if it is present
		$projectDb = \models\mapper\MongoStore::connect($projectModel->databaseName());
		foreach ($projectDb->listCollections() as $collection) {
			$collection->drop();
		}
	}
	
	/**
	 * Returns a string very much like those used for MongoIds
	 * @return string
	 */
	public static function mockId() {
		$id = new MongoId();
		return (string)$id;
	}
	
	public function inhibitErrorDisplay() {
		$this->_display = ini_get('display_errors');
		ini_set('display_errors', false);
	}
	
	public function restoreErrorDisplay() {
		ini_set('display_errors', $this->_display);
	}
		
}

?>