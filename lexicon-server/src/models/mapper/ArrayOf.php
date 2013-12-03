<?php
namespace models\mapper;

class ArrayOf extends \ArrayObject {
	
	/**
	 * @var function The function <object> function($data = null) returns an instance of the object.
	 */
	private $_generator;
	
	private $data; // This is here to force client code using the older implementation to have a fatal error allowing us to identify code that needs upgradeing. CP 2013-12
	
	/**
	 * @param string Either ArrayOf::VALUE or ArrayOf::OBJECT
	 * @param function The function <object> function($data = null) returns an instance of the object.
	 */
	public function __construct($generator = null) {
		$this->_generator = $generator;
	}
	
	public function generate($data = null) {
		$function = $this->_generator;
		return $function($data);
	}
	
	public function hasGenerator() {
		return $this->_generator != null;
	}
	
}

?>