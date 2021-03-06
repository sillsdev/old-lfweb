<?php
namespace libraries\lfdictionary\commands;
use libraries\lfdictionary\environment\LexProject;

use \libraries\lfdictionary\mapper\TaskSettingXmlJsonMapper;

require_once(dirname(__FILE__) . '/../Config.php');

use \libraries\lfdictionary\environment\LexProjectUserSettings;

class GetSettingUserTasksSettingCommand
{

	/**
	 * @var JSON
	 */
	private $_result;


	/**
	 * @param string
	 */
	private $_userName;
	
	/**
	 * 
	 * @var LexProject
	 */
	private $_lexProject;

	function __construct($lexProject, $userName) {
		$this->_lexProject = $lexProject; // lexProject
		$this->_userName = $userName;
	}

	function execute() {
		$this->processFile();
		return $this->_result;
	}

	function processFile() {
		
		$configFilePath = $this->_lexProject->getUserOrDefaultProjectSettingsFilePath($this->_userName);
		$xml_str = file_get_contents($configFilePath);
		$doc = new \DOMDocument;
		$doc->preserveWhiteSpace = FALSE;
		$doc->loadXML($xml_str);
		$componentsDoc = new \DomDocument;
		$componentsDoc->appendChild($componentsDoc->importNode($doc->getElementsByTagName("tasks")->item(0), true));
		$this->_result = TaskSettingXmlJsonMapper::encodeTaskXmlToJson($componentsDoc);
	}
};

?>
