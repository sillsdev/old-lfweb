<?php
require_once(dirname(__FILE__) . '/../TestConfig.php');
require_once(SIMPLETEST_PATH . 'autorun.php');

class AllApiTests extends TestSuite {
	
    function __construct() {
        parent::__construct();
 		$this->addFile(TEST_PATH . 'api/UserAPI_Test.php');
 		$this->addFile(TEST_PATH . 'api/ProjectAPI_Test.php');
    }

}

?>