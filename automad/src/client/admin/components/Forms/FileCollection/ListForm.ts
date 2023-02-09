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

import { create, CSS, EventName, fire, listen, queryAll } from '../../../core';
import { File, KeyValueMap } from '../../../types';
import { FormComponent } from '../Form';

/**
 * The file collection form component.
 *
 * @example
 * <am-file-collection-list-form ${Attr.api}="FileCollection/list"></am-file-collection-list-form>
 * <am-submit ${Attr.form}="FileCollection/list">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class FileCollectionListFormComponent extends FormComponent {
	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Initialize the form.
	 */
	protected init(): void {
		super.init();

		this.classList.add(CSS.grid);
		this.setAttribute('style', '--min: 12rem; --aspect: 1.25;');

		this.listeners.push(
			listen(
				window,
				`${EventName.appStateChange} ${EventName.filesChangeOnServer}`,
				this.refresh.bind(this)
			)
		);
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		await super.processResponse(response);

		if (typeof response.data == 'undefined') {
			return;
		}

		this.innerHTML = '';

		if (typeof response.data.files == 'undefined') {
			return;
		}

		response.data.files.forEach((file: File[]) => {
			const card = create('am-file-card', [], {}, this);

			card.data = file;
		});

		fire(EventName.fileCollectionRender);
	}

	/**
	 * Reset the selection and submit the form in order to refresh the list of files.
	 */
	refresh(): void {
		queryAll('[type="checkbox"]', this).forEach(
			(checkbox: HTMLInputElement) => {
				checkbox.checked = false;
			}
		);

		this.submit(true);
	}
}

customElements.define(
	'am-file-collection-list-form',
	FileCollectionListFormComponent
);
