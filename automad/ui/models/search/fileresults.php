<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Models\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A wrapper class for all results for a given data file.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileResults {
	/**
	 * The array of `FieldResults`.
	 *
	 * @see \Automad\UI\Models\Search\FieldResults
	 */
	public $fieldResultsArray;

	/**
	 * The file path.
	 */
	public $path;

	/**
	 * The page URL or false for shared data.
	 */
	public $url;

	/**
	 * Initialize a new field results instance.
	 *
	 * @see \Automad\UI\Models\Search\FieldResults
	 * @param string $path
	 * @param array $fieldResultsArray
	 * @param string $url
	 */
	public function __construct($path, $fieldResultsArray, $url = false) {
		$this->path = $path;
		$this->fieldResultsArray = $fieldResultsArray;
		$this->url = $url;
	}
}
