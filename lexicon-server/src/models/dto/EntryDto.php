<?php

namespace models\dto;

use libraries\lfdictionary\store\LexStoreType;
use libraries\lfdictionary\store\LexStoreController;
use models\ProjectModel;
use models\QuestionAnswersListModel;
use models\UserModel;
use libraries\lfdictionary\store\LexStore;
use libraries\lfdictionary\environment\LexProject;
use models\ProjectModelFixer;

require_once (APPPATH . 'libraries/lfdictionary/Config.php');

// TODO This is most likely the DictionaryBrowseEditDto CP 2013-11.  This actually looks quite naf. Would be surprised if this does anything useful at all really.
class EntryDto
{
	/**
	 *
	 * @param string $projectId
	 * @param string $textId
	 * @param string $userId
	 * @returns array - the DTO array
	 */
	public static function encode($projectId, $entryGuid) {
		
		$projectModel = new ProjectModel ( $projectId );
		ProjectModelFixer::ensureVLatest ( $projectModel );
	
		$lexProject = new LexProject ( $projectModel );
	
		$store = new LexStoreController ( LexStoreType::STORE_MONGO, $projectModel->databaseName (), $lexProject );
		$result = $store->readEntry ( $entryGuid );
	
		// Sense Level
		foreach ( $result->_senses as $sense ) {
	
			if (! (isset ( $sense->_id ) && strlen ( trim ( $sense->_id ) ) > 0)) {
				$sense->_id = \libraries\lfdictionary\common\UUIDGenerate::uuid_generate_php ();
			}
			// Example Level
			foreach ( $sense->_examples as $example ) {
				if (! (isset ( $example->_id ) && strlen ( trim ( $example->_id ) ) > 0)) {
					$example->_id = \libraries\lfdictionary\common\UUIDGenerate::uuid_generate_php ();
				}
			}
		}
		return $result->encode ();
	}
}

?>
