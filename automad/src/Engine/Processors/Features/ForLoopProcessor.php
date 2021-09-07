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

namespace Automad\Engine\Processors\Features;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The for loop processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ForLoopProcessor extends AbstractFeatureProcessors {
	public function process(array $matches, string $directory) {
		if (!empty($matches['forSnippet'])) {
			$start = intval($this->ContentProcessor->processVariables($matches['forStart']));
			$end = intval($this->ContentProcessor->processVariables($matches['forEnd']));
			$html = '';

			$TemplateProcessor = $this->initTemplateProcessor();

			// Save the index before any loop - the index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
			$runtimeShelf = $this->Runtime->shelve();

			// The loop.
			for ($i = $start; $i <= $end; $i++) {
				// Set index variable. The index can be used as @{:i}.
				$this->Runtime->set(AM_KEY_INDEX, $i);
				// Parse snippet.
				Debug::log($i, 'Processing snippet in loop for index');
				$html .= $TemplateProcessor->process($matches['forSnippet'], $directory);
			}

			// Restore index.
			$this->Runtime->unshelve($runtimeShelf);

			return $html;
		}
	}
}
