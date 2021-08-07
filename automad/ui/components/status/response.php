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
 * Copyright (c) 2019-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Status;

use Automad\Core\Debug;
use Automad\Core\Str;
use Automad\System\Update;
use Automad\UI\Utils\Text;
use Automad\UI\Controllers\Headless;
use Automad\UI\Controllers\PackageManager;
use Automad\UI\Models\Accounts;
use Automad\UI\Response as UIResponse;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The status response component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Response {
	/**
	 * 	Get the current status response of a given system item or packages.
	 *
	 * @param string $item
	 * @return \Automad\UI\Response the response object
	 */
	public static function render($item) {
		Debug::log($item, 'Getting status');
		$Response = new UIResponse();

		if ($item == 'cache') {
			if (AM_CACHE_ENABLED) {
				$Response->setStatus(
					'<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_cache_enabled')
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_cache_disabled')
				);
			}
		}

		if ($item == 'debug') {
			$Response->setStatus('');
			$tooltip = Text::get('sys_status_debug_enabled');
			$tab = Str::sanitize(Text::get('sys_debug'));

			if (AM_DEBUG_ENABLED) {
				$html = <<< HTML
					<a 
					href="?view=System#$tab" 
					class="am-u-button am-u-button-danger" 
					title="$tooltip" 
					data-uk-tooltip="{pos:'bottom-right'}"
					>
						<i class="am-u-icon-bug"></i>
					</a>
HTML;
				$Response->setStatus($html);
			}
		}

		if ($item == 'headless_template') {
			$template = Str::stripStart(Headless::getTemplate(), AM_BASE_DIR);
			$badge = '';

			if ($template != AM_HEADLESS_TEMPLATE) {
				$badge = ' uk-badge-success';
			}

			$Response->setStatus(
				'<span class="uk-badge uk-badge-notification uk-margin-top-remove' . $badge . '">' .
				'<i class="uk-icon-file-text"></i>&nbsp&nbsp;' .
				trim($template, '\\/') .
				'</span>'
			);
		}

		if ($item == 'update') {
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$Response->setStatus(
					'<i class="uk-icon-download uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_update_available') .
					'&nbsp;&nbsp;<span class="uk-badge uk-badge-success">' . $updateVersion . '</span>'
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-check uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_update_not_available')
				);
			}
		}

		if ($item == 'update_badge') {
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$Response->setStatus(
					'<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i></span>'
				);
			}
		}

		if ($item == 'users') {
			$Response->setStatus(
				'<i class="uk-icon-users uk-icon-justify"></i>&nbsp;&nbsp;' .
				Text::get('sys_user_registered') .
				'&nbsp;&nbsp;<span class="uk-badge">' . count(Accounts::get()) . '</span>'
			);
		}

		if ($item == 'outdated_packages') {
			$Response = PackageManager::getOutdatedPackages();

			if ($Response->getBuffer()) {
				$data = json_decode($Response->getBuffer());

				if (!empty($data->installed)) {
					$count = count($data->installed);
					$Response->setStatus(
						'<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i>&nbsp); ' .
						$count . '</span>'
					);
				}
			}
		}

		return $Response;
	}
}
