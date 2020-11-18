<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Config class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2014-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Config {
	
	
	/**
	 *	Read configuration overrides as JSON string form PHP or JSON file 
	 *	and decode the returned string.
	 *	
	 *	@return array The configuration array
	 */
	 
	public static function read() {
		
		$json = false;
		$config = array();
		$legacy = AM_BASE_DIR . '/config/config.json';

		if (is_readable(AM_CONFIG)) {
			$json = require AM_CONFIG;
		} else if (is_readable($legacy)) {
			// Support legacy configuration files.
			$json = file_get_contents($legacy);
		}

		if ($json) {
			$config = json_decode($json, true); 
		}
		
		return $config;

	}


	/**
	 *	Write the configuration file.
	 *	
	 *	@param array $config 
	 *	@return boolean True on success
	 */

	public static function write($config) {

		$json = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$content = "<?php return <<< JSON\n\r$json\n\rJSON;";

		return FileSystem::write(AM_CONFIG, $content);

	}


	/**
	 *	Define constants based on the configuration array.
	 */

	public static function overrides() {

		foreach (self::read() as $name => $value) {
			define($name, $value);	
		}

	}
	
	
	/**
	 * 	Define constant, if not defined already.
	 * 
	 *	@param string $name
	 *	@param string $value
	 */
	 
	public static function set($name, $value) {
	
		if (!defined($name)) {
			define($name, $value);
		}
	
	}
	
	
}
