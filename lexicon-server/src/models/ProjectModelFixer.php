<?php
namespace models;

use libraries\palaso\CodeGuard;

class ProjectModelFixer
{
	/**
	 * @var ProjectModel
	 */
	public $projectModel;
	
	/**
	 * @var bool
	 */
	public $wasFixed;

	/**
	 * @param ProjectModel $projectModel
	 */
	public function __construct($projectModel) {
		CodeGuard::checkTypeAndThrow($projectModel, 'models\ProjectModel');
		$this->projectModel = $projectModel;
	}
	
	public static function ensureVLatest($projectModel) {
		$fixer = new ProjectModelFixer($projectModel);
		
		$fixer->ensureV01();
		
		if ($fixer->wasFixed) {
			$fixer->projectModel->write();
		}
	}
	
	private function ensureV01() {
		$model = $this->projectModel;
		$this->ensureHasProjectCode();
		$this->ensureHasProjectName();
		$this->ensureHasLanguageCode();
		$this->ensureHasProjectType();
	}
	
	private function ensureHasProjectCode() {
		$model = $this->projectModel;
		if (!empty($model->projectCode)) {
			return;
		}
		$this->ensureHasProjectName();
		$this->ensureHasLanguageCode();
		$this->ensureHasProjectType();
		$model->projectCode = ProjectModel::makeProjectCode($model->languageCode, $model->projectname, $model->projectType);
		error_log(sprintf("Fixed ProjectModel::projectCode %s = %s", $model->id->asString(), $model->projectCode));
		$this->wasFixed = true;
	}
	
	private function ensureHasProjectName() {
		$model = $this->projectModel;
		if (!empty($model->projectname)) {
			return;
		}
		$model->projectname = 'Unknown Project';
		error_log(sprintf("Fixed ProjectModel::projectName %s = %s", $model->id->asString(), $model->projectname));
		$this->wasFixed = true;
	}
	
	private function ensureHasLanguageCode() {
		$model = $this->projectModel;
		if (!empty($model->languageCode)) {
			return;
		}
		$model->languageCode = 'qaa';
		error_log(sprintf("Fixed ProjectModel::languageCode %s = %s", $model->id->asString(), $model->languageCode));
		$this->wasFixed = true;
	}
	
	private function ensureHasProjectType() {
		$model = $this->projectModel;
		if (!empty($model->projectType)) {
			return;
		}
		$model->projectType = ProjectModel::PROJECT_LIFT;
		error_log(sprintf("Fixed ProjectModel::projectType %s = %s", $model->id->asString(), $model->projectType));
		$this->wasFixed = true;
	}
}

?>