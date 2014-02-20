<?php
use libraries\lfdictionary\environment\LexProject;

use libraries\lfdictionary\common\HgWrapper;
use models\ProjectModel;
class LexProjectTestEnvironment {
	
	/**
	 * @var string
	 */
	public $projectWorkPath;
	
	/**
	 * 
	 * @var string
	 */
	public $projectSlug;
	
	/**
	 * @var HgWrapper
	 */
	private $_hg;
	
	/**
	 * 
	 * @var LexProject
	 */
	public $lexProject;
	
	const PROJECT_NAME = 'LexProject_Test';
	
	function __construct($projectName = self::PROJECT_NAME, $projectWorkPath = null, $doInit = false) {
		$this->projectSlug = ProjectModel::makeProjectSlug('qaa', $projectName, "dictionary");
		if ($projectWorkPath == null) {
			$this->projectWorkPath = self::normalizePath(sys_get_temp_dir());
		}
		$this->cleanup();
		$this->_hg = new HgWrapper($this->getProjectPath());
		if ($doInit) {
			if (!file_exists($this->getProjectPath())) {
				mkdir($this->getProjectPath());
			}
			$this->_hg->init();
		}
		$model= new ProjectModel();
		$model->projectName = $projectName;
		$model->projectSlug = $this->projectSlug;
		$this->lexProject = new LexProject($model, '/tmp/');
	}
	
	function __destruct() {
		$this->cleanup();
	}

	public function getProjectPath($projectSlug = null) {
		if ($projectSlug == null) {
			$projectSlug = $this->projectSlug;
		}
		return self::normalizePath($this->projectWorkPath . $projectSlug);
	}
	
	public function cleanup($projectSlug = null) {
 		self::recursiveDelete($this->getProjectPath($projectSlug));
		if ($projectSlug == null) {
			$projectSlug = $this->projectSlug;
		}
		$stateFile = LexProject::stateFolderPath() . $projectSlug . '.state';
		if (file_exists($stateFile)) {
			unlink($stateFile);
		}
	}
	
	static public function recursiveDelete($str) {
		if(is_file($str)) {
			return @unlink($str);
		} elseif(is_dir($str)) {
			$str = self::normalizePath($str);
			$objects = scandir($str);
			foreach ($objects as $object) {
				if ($object === "." || $object === "..") {
					continue;
				}
				self::recursiveDelete($str . $object);
			}
			reset($objects);
			@rmdir($str);
		}
	}
	
	static private function normalizePath($path) {
		$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		return $path;
	}
	
	public function addFile($fileName, $contents) {
		$filePath = $this->getProjectPath() . $fileName;
		file_put_contents($filePath, $contents);
		$this->_hg->addFile($filePath);
		$this->_hg->commit("File added");
	}
	
}
?>