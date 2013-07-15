<?php
require_once(dirname(__FILE__) . '/../testconfig.php');
require_once(SIMPLETEST_PATH . 'autorun.php');
require_once(LF_BASE_PATH . "/lfbase/Loader.php");

class TestOfProjectStateDTO extends UnitTestCase {

	function testProjectStateDTO_Encode_JsonCorrect() {
		$dto = new \dto\ProjectStateDTO("Result", "Message");
		$dto->Progress=10;
		$result = json_encode($dto->encode());
		
		$this->assertEqual($result, '{"result":"Result","message":"Message","progress":10}');
	}

}

?>