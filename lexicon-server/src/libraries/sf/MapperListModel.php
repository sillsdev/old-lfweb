<?php

namespace libraries\sf;

if (!defined('LF_DATABASE'))
{
	define('LF_DATABASE', 'languageforge');
}

class MapperListModel /*extends CI_Model*/
{
	/**
	 * @var int
	 */
	public $count;
	
	/**
	 * @var array
	 */
	public $entries;
	
	/**
	 * @var MongoMapper
	 */
	protected $_mapper;

	/**
	 * @var array
	 */
	protected $_query;

	/**
	 * @var array
	 */
	protected $_fields;
	
	/**
	 * @param MongoMapper $mapper
	 * @param array $query
	 * @param array $fields
	 */
	protected function __construct($mapper, $query, $fields = array())
	{
		$this->_mapper = $mapper;
		$this->_query = $query;
		$this->_fields = $fields;
	}

	function read()
	{
		return $this->_mapper->readList($this, $this->_query, $this->_fields);
	}
	
}

?>