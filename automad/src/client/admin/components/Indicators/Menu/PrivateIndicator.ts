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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, create, CSS, EventName, getPageURL, listen } from '../../../core';
import { BaseComponent } from '../../Base';

/**
 * A private indicator badge for pages.
 *
 * @extends BaseComponent
 */
class PrivateIndicatorComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.privacyIndicator);

		this.listeners.push(
			listen(window, EventName.appStateChange, this.render.bind(this))
		);

		this.render();
	}

	/**
	 * Render the indicator.
	 */
	private render(): void {
		const url = getPageURL();

		this.innerHTML = '';

		if (App.pages[url]?.private) {
			create('i', ['bi', 'bi-eye-slash-fill'], {}, this);
		} else {
			create('i', ['bi', 'bi-eye'], {}, this);
		}
	}
}

customElements.define('am-private-indicator', PrivateIndicatorComponent);
