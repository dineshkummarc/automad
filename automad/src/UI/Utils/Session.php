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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Utils;

use Automad\UI\Models\UserCollectionModel;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session model class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Session {
	/**
	 * Return the currently logged in user.
	 *
	 * @return string Username
	 */
	public static function getUsername() {
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
	}

	/**
	 * Verify login information based on $_POST.
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool false on error
	 */
	public static function login(string $username, string $password) {
		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser($username);

		if (empty($User)) {
			return false;
		}

		if ($User->verifyPassword($password)) {
			session_regenerate_id(true);
			$_SESSION['username'] = $username;

			// In case of using a proxy,
			// it is safer to just refresh the current page instead of rebuilding the currently requested URL.
			header('Refresh:0');

			exit();
		}

		return false;
	}

	/**
	 * Log out user.
	 *
	 * @return bool true on success
	 */
	public static function logout() {
		unset($_SESSION);
		$success = session_destroy();

		if (!isset($_SESSION) && $success) {
			return true;
		} else {
			return false;
		}
	}
}
