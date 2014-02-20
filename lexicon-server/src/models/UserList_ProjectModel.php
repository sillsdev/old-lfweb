<?php
namespace models;

use models\mapper\MongoMapper;

use models\mapper\MapperListModel;
use models\UserModelMongoMapper;

/**
 * List of users who are members of the specified project
 */
class UserList_ProjectModel extends \models\mapper\MapperListModel
{

	public function __construct($projectId)
	{
		parent::__construct(
				UserModelMongoMapper::instance(),
				array('projects' => array('$in' => array(MongoMapper::mongoID($projectId)))),
				array('username', 'email', 'name')
		);
	}

}
?>