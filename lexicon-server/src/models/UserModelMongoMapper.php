<?php
namespace models;

use \models\mapper\MongoMapper;

class UserModelMongoMapper extends \models\mapper\MongoMapper
{
	public static function instance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new UserModelMongoMapper(LF_DATABASE, 'users');
		}
		return $instance;
	}
}


?>