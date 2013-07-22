<?php

namespace models;

use libraries\sf\MongoStore;
use libraries\sf\ReferenceList;

require_once(APPPATH . '/models/ProjectModel.php');

class ProjectModelMongoMapper extends \libraries\sf\MongoMapper
{
	public static function instance()
	{
		static $instance = null;
		if (null === $instance)
		{
			$instance = new ProjectModelMongoMapper(LF_DATABASE, 'projects');
		}
		return $instance;
	}
	
	public function drop($databaseName) {
		if (MongoStore::hasDB($databaseName)) {
			$db = MongoStore::connect($databaseName);
			$db->drop();
		}
	}
}

class ProjectModel extends \libraries\sf\MapperModel
{
	public function __construct($id = NULL)
	{
		$this->users = new ReferenceList();
		parent::__construct(ProjectModelMongoMapper::instance(), $id);
	}
	
	public function databaseName() {
		$name = strtolower($this->projectname);
		$name = str_replace(' ', '_', $name);

		return 'lf_' . $name;

	}

	/**
	 * Removes this project from the collection.
	 * User references to this project are also removed
	 */
	public function remove()
	{
		ProjectModelMongoMapper::instance()->drop($this->databaseName());
		ProjectModelMongoMapper::instance()->remove($this->id);
	}
	
	
	/**
	 * Adds the $userId as a member of this project.
	 * You do NOT need to call write() as this method calls it for you
	 * @param string $userId
	 */
	public function addUser($userId) {
		$this->users->_addRef($userId);

	}
	
	
	/**
	 * Removes the $userId from this project.
	 * You do NOT need to call write() as this method calls it for you
	 * @param string $userId
	 */
	public function removeUser($userId) {
		//$userModel = new UserModel($userId);
		$this->users->_removeRef($userId);
		//$userModel->projects->_removeRef($this->id);
	}

	public function listUsers() {
		$userList = new UserList_ProjectModel($this->id);
		$userList->read();
		return $userList;
	}
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $projectname;
	
	/**
	 * @var string
	 */
	public $language;
	
	/**
	 * @var ReferenceList
	 */
	public $users;
	
	
	/**
	* @var string
	*/
	public $title;
	
	// What else needs to be in the model?
	
}


?>