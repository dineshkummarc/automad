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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Models\AppModel;
use Automad\Admin\Models\UserCollectionModel;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to the app state of the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AppController {
	/**
	 * Send the minimum static data that is required to bootstrap the dashboard app.
	 *
	 * @return Response the Response object
	 */
	public static function bootstrap() {
		$Response = new Response;
		$ThemeCollection = new ThemeCollection();

		$Response->setData(array(
			'version' => AM_VERSION,
			'text' => Text::getObject(),
			'themes' => $ThemeCollection->getThemes(),
			'base' => AM_BASE_URL,
			'baseIndex' => AM_BASE_INDEX,
			'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
			'reservedFields' => Fields::$reserved,
			'allowedFileTypes' => FileUtils::allowedFileTypes()
		));

		return $Response;
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * @return Response the Response object
	 */
	public static function updateState() {
		$Response = new Response;
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$UserCollectionModel = new UserCollectionModel();

		$Response->setData(array(
			'tags' => $Automad->getPagelist()->getTags(),
			'pages' => AppModel::pages($Automad),
			'sitename' => $Automad->Shared->get(AM_KEY_SITENAME),
			'mainTheme' => $Automad->Shared->get(AM_KEY_THEME),
			'system' => array(
				'cache' => array(
					'enabled' => AM_CACHE_ENABLED,
					'lifetime' => AM_CACHE_LIFETIME,
					'monitorDelay' => AM_CACHE_MONITOR_DELAY
				),
				'debug' => AM_DEBUG_ENABLED,
				'feed' => array(
					'enabled' => AM_FEED_ENABLED,
					'fields' => AM_FEED_FIELDS
				),
				'translation' => AM_FILE_UI_TRANSLATION,
				'users'=> $UserCollectionModel->getCollection(),
				'tempDirectory' => FileSystem::getTmpDir()
			)
		));

		return $Response;
	}
}