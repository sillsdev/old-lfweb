<?php

namespace libraries\lfdictionary\common;

// TODO Delete. See ErrorHandler

/**
 * Factory for FileLogger
 *
 * @version    0.7
 * @package    Logger
 *
 * @author Daniel Milde <daniel@milde.cz>
 */
interface ILoggerFactory
{
	/**
	 * @param array $options
	 * @return Logger\ILogger
	 */
	public function factory(array $options = array());
}
