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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Blocks class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Blocks {
	/**
	 * Inject block assets into the header of a page.
	 *
	 * @param string $str
	 * @return string the processed HTML
	 */
	public static function injectAssets(string $str) {
		$versionSanitized = Str::sanitize(AM_VERSION);
		$css = '/automad/dist/blocks.min.css?v=' . $versionSanitized;
		$js = '/automad/dist/blocks.min.js?v=' . $versionSanitized;

		$assets = '<link rel="stylesheet" href="' . $css . '">';
		$assets .= '<script type="text/javascript" src="' . $js . '"></script>';

		// Check if there is already any other script tag and try to prepend all assets as first items.
		if (preg_match('/\<(script|link).*\<\/head\>/is', $str)) {
			return preg_replace('/(\<(script|link).*\<\/head\>)/is', $assets . '$1', $str);
		} else {
			return str_replace('</head>', $assets . '</head>', $str);
		}
	}

	/**
	 * Render blocks created by the EditorJS block editor.
	 *
	 * @param string $json
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(string $json, Automad $Automad) {
		$flexOpen = false;
		$data = json_decode($json);
		$html = '';

		if (!is_object($data)) {
			return false;
		}

		if (!isset($data->blocks)) {
			return false;
		}

		foreach ($data->blocks as $block) {
			try {
				$blockIsFlexItem = (!empty($block->data->widthFraction) && empty($block->data->stretched));

				if (!$flexOpen && $blockIsFlexItem) {
					$html .= '<am-flex>';
					$flexOpen = true;
				}

				if ($flexOpen && !$blockIsFlexItem) {
					$html .= '</am-flex>';
					$flexOpen = false;
				}

				$blockHtml = call_user_func_array(
					'\\Automad\\Blocks\\' . ucfirst($block->type) . '::render',
					array($block->data, $Automad)
				);

				// Stretch block.
				if (!empty($block->data->stretched)) {
					$blockHtml = "<am-stretched>$blockHtml</am-stretched>";
				} elseif (!empty($block->data->widthFraction)) {
					$widthFraction = str_replace('/', '-', $block->data->widthFraction);
					$blockHtml = "<am-{$widthFraction}>$blockHtml</am-{$widthFraction}>";
				}

				$html .= $blockHtml;
			} catch (\Exception $e) {
				continue;
			}
		}

		if ($flexOpen) {
			$html .= '</am-flex>';
		}

		return $html;
	}
}
