<?php
require_once(dirname(__FILE__) . '/../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once(TestLibPath . 'jsonRPCClient.php');

class TestProjectAPI extends UnitTestCase {

	function __construct() {
	}
	
	function testProjectCRUD_CRUDOK() {
		$api = new jsonRPCClient("http://scriptureforge.local/api/sf", false);
		
		// Create
		$param = array(
			'id' => '',
			'projectName' => LF_TESTPROJECT,
			'language' => 'SomeLanguage'
		);
		$id = $api->project_update($param);
		$this->assertNotNull($id);
		$this->assertEqual(24, strlen($id));
		
		// Read
		$result = $api->project_read($id);
		$this->assertNotNull($result['id']);
		$this->assertEqual(LF_TESTPROJECT, $result['projectName']);
		$this->assertEqual('SomeLanguage', $result['language']);
		
		// Update
		$result['language'] = 'AnotherLanguage';
		$id = $api->project_update($result);
		$this->assertNotNull($id);
		$this->assertEqual($result['id'], $id);
		
		// Delete
 		$result = $api->project_delete(array($id));
 		$this->assertTrue($result);
		
	}
	
	function testProjectList_Ok() {
		$api = new jsonRPCClient("http://scriptureforge.local/api/sf", false);
		$result = $api->project_list();
		
		$this->assertTrue($result['count'] > 0);
		
	}
	
}

?>
