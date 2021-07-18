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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\UI\Components\InPage\Edit;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The inPage controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class InPage extends Page {


	/**
	 *	Handle AJAX request for editing a data variable in-page context.
	 *
	 *	If no data gets received, form fields to build up the editing dialog are send back. 
	 *	Else the received data gets merged with the full data array of the requested context and 
	 *	saved back into the .txt file. 
	 *	In case the title variable gets modified, the page directory gets renamed accordingly.
	 *
	 *	@return array $output
	 */

	public static function edit() {

		$Automad = UICache::get();
		$output = array();
		$context = Request::post('context');
		$postData = Request::post('data');
		$url = Request::post('url');

		if ($context) {

			// Check if page actually exists.
			if ($Page = $Automad->getPage($context)) {

				// If data gets received, merge and save.
				// Else send back form fields.
				if ($postData && is_array($postData)) {

					// Merge and save data.
					$data = array_merge(Parse::textFile(self::getPageFilePath($Page)), $postData);
					FileSystem::writeData($data, self::getPageFilePath($Page));
					Debug::log($data, 'saved data');
					Debug::log(self::getPageFilePath($Page), 'data file');

					// If the title has changed, the page directory has to be renamed as long as it is not the home page.
					if (!empty($postData[AM_KEY_TITLE]) && $Page->url != '/') {

						// Move directory.
						$newPagePath = FileSystem::movePageDir(
							$Page->path,
							dirname($Page->path),
							self::extractPrefixFromPath($Page->path),
							$postData[AM_KEY_TITLE]
						);

						Debug::log($newPagePath, 'renamed page');

					}

					// Clear cache to reflect changes.
					Cache::clear();

					// If the page directory got renamed, find the new URL.
					if ($Page->url == $url && isset($newPagePath)) {

						// The page has to be redirected to a new url in case the edited context is actually 
						// the requested page and the title of the page and therefore the URL has changed.

						// Rebuild Automad object, since the file structure has changed.
						$Automad = UICache::rebuild();

						// Find new URL and return redirect URL.
						foreach ($Automad->getCollection() as $key => $Page) {

							if ($Page->path == $newPagePath) {
								$output['redirect'] = AM_BASE_INDEX . $key;
								break;
							}

						}

					} else {

						// There are two cases where the currently requested page has to be
						// simply reloaded without redirection:
						// 
						// 1.	The context of the edits is not the current page and another
						// 		pages gets actually edited.
						// 		That would be the case for edits of pages displayed in pagelists or menus.
						// 	
						// 2.	The context is the current page, but the title didn't change and
						// 		therefore the URL stays the same.
						$output['redirect'] = AM_BASE_INDEX . $url;

					}

					// Append query string if not empty.
					$queryString = Request::post('query');

					if ($queryString) {
						$output['redirect'] .= '?' . $queryString;
					}

				} else {

					// Return form fields if key is defined.
					if ($key = Request::post('key')) {

						$value = '';

						if (!empty($Page->data[$key])) {
							$value = $Page->data[$key];
						}

						// Note that $Page->path has to be added to make 
						// image previews work in CodeMirror.
						$output['html'] = Edit::render($Automad, $key, $value, $context, $Page->path);

					}

				}

			}

		}

		return $output;

	}
	

}