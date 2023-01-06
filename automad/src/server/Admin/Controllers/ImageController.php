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

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Models\ImageModel;
use Automad\Admin\UI\Utils\Messenger;
use Automad\Core\Automad;
use Automad\Core\FileSystem;
use Automad\Core\Request;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageController {
	/**
	 * Save an image that was modified in FileRobot.
	 *
	 * @return Response the response object
	 */
	public static function save() {
		$Response = new Response();
		$Messenger = new Messenger();
		$Automad = Automad::fromCache();
		$path = FileSystem::getPathByPostUrl($Automad);

		ImageModel::save(
			$path,
			Request::post('name'),
			Request::post('extension'),
			Request::post('imageBase64'),
			$Messenger
		);

		return $Response->setError($Messenger->getError());
	}

	/**
	 * Select an image.
	 *
	 * @return Response the response object
	 */
	public static function select() {
		$Response = new Response();

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		//$Response->setHtml(ImageModel::select(Request::post('url')));

		return $Response;
	}
}
