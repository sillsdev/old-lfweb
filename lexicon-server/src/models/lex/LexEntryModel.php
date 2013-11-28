<?php

namespace models\lex;

class LexEntryModel {

	/**
	 *
	 * @var string
	 */
	var $_guid;

	/**
	 *
	 * @var string
	 */
	var $mercurialSHA;

	/**
	 * This is a single LF domain
	 * @var MultiText
	 */
	var $_entry; // TODO Rename 'lexeme'

	/**
	 * @var array<Sense>
	 */
	var $_senses;

	/**
	 *
	 * @var AuthorInfoModel
	 */
	var $_metadata;

	private function __construct($guid=null) {
		$this->_guid = $guid;
		$this->_entry = new MultiText();
		$this->_senses = array();
		$this->_metadata = new AuthorInfoModel();
	}

	/**
	 * @param string $guid
	 */
	function setGuid($guid) {
		$this->_guid = $guid;
	}

	/**
	 * @return string
	 */
	function getGuid() {
		return $this->_guid;
	}

	/**
	 * @param string $mercurialSHA
	 */
	function setMercurialSHA($mercurialSHA) {
		$this->mercurialSHA = $mercurialSHA;
	}

	/**
	 * @return string
	 */
	function getMercurialSHA() {
		return $this->mercurialSHA;
	}

	/**
	 * @param MultiText $multitext
	 */
	function setEntry($multitext) {
		$this->_entry = $multitext;
	}

	/**
	 * @return MultiText
	 */
	function getEntry() {
		return $this->_entry;
	}

	/**
	 * @param Sense $sense
	 */
	function addSense($sense) {
		$this->_senses[] = $sense;
	}

	/**
	 * @return int
	 */
	function senseCount() {
		return count($this->_senses);
	}

	/**
	 * @param int $index
	 * @return Sense
	 */
	function getSense($index) {
		return $this->_senses[$index];
	}

	/**
	 * Encodes the object into a php array, suitable for use with json_encode
	 * @return mixed
	 */
	function encode() {
		$senses = array();
		foreach ($this->_senses as $sense) {
			$senses[] = $sense->encode();
		}
		return array(
				"guid" => $this->_guid,
				"mercurialSHA" => $this->mercurialSHA,
				"entry" => $this->_entry->encode(),
				"senses" => $senses,
				"metadata" => $this->_metadata->encode()
		);
	}

	/**
	 * Decodes the given mixed object into a new LexEntryModel
	 * @param mixed $value
	 * @return LexEntryModel
	 */
	function decode($value) {
		if ($value == null) {
			return;
		}
		$this->_metadata = new AuthorInfoModel();
		$this->_guid = $value['guid'];
		$this->mercurialSHA = $value['mercurialSHA'];
		$this->_entry = MultiText::createFromArray($value['entry']);
		if (isset($value['metadata'])) {
			$this->_metadata = AuthorInfoModel::createFromArray($value['metadata']);
		}

		foreach ($value['senses'] as $senseValue) {
			$sense = Sense::createFromArray($senseValue);
			$this->addSense($sense);
		}
	}

	/**
	 * @return LexEntryModel
	 */
	static function create($guid) {
		return new LexEntryModel($guid);
	}

	/**
	 * @return LexEntryModel
	 */
	static function createFromArray($value) {
		$result = new LexEntryModel();
		$result->decode($value);
		return $result;
	}

}

?>