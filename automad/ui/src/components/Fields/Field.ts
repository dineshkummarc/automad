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
 */

import {
	App,
	classes,
	create,
	html,
	htmlSpecialChars,
	query,
	queryAll,
	titleCase,
} from '../../core';
import { FieldInitData, FieldRenderData, InputElement } from '../../types';
import { BaseComponent } from '../Base';

/**
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
const createId = (key: string): string => {
	return `am-id-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param key
 * @returns the generated label
 */
const createLabel = (key: string): string => {
	return titleCase(key.replace(/\+(.)/, '+ $1'))
		.replace('+ ', '+')
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

/**
 * A standard input field with a label.
 *
 * Fields can have several attributes:
 * - `required` - with and empty value, a form can't be submitted
 *
 * @extends BaseComponent
 */
export class FieldComponent extends BaseComponent {
	/**
	 * If true the field data is sanitized.
	 */
	protected sanitize = true;

	/**
	 * The internal field data.
	 */
	protected _data: FieldRenderData;

	/**
	 * Get the actual field input element.
	 */
	get input(): InputElement {
		return query('[name]', this) as InputElement;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.field);
	}

	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.key
	 * @param params.value
	 * @param params.name
	 * @param params.tooltip
	 * @param params.label
	 * @param params.placeholder
	 */
	set data({ key, value, name, tooltip, label, placeholder }: FieldInitData) {
		const id = createId(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabel(key);
		placeholder = placeholder || '';

		if (typeof value === 'string' && this.sanitize) {
			value = htmlSpecialChars(value);
		}

		this._data = {
			name,
			id,
			label,
			value,
			tooltip,
			placeholder,
		};

		this.init();
	}

	/**
	 * Create a label.
	 */
	protected createLabel(): void {
		const { id, label, tooltip } = this._data;
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = html`
			<label class="${classes.fieldLabel}" for="${id}" title="${tooltip}">
				${label}
			</label>
		`;
	}

	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		create(
			'input',
			[classes.input],
			{ id, name, value, type: 'text', placeholder },
			this
		);
	}

	/**
	 * Render the field.
	 */
	protected init(): void {
		this.createLabel();
		this.createInput();
		this.applyAttributes();
	}

	/**
	 * Apply field attributes to actual input elements.
	 */
	private applyAttributes(): void {
		queryAll('input, textarea', this).forEach((input: InputElement) => {
			if (this.hasAttribute('required')) {
				input.setAttribute('pattern', '.*\\S.*');
				input.setAttribute('placeholder', App.text('requiredField'));
				input.setAttribute('required', '');
				this.removeAttribute('required');
			}

			['bind', 'bindto', 'toggle'].forEach((attr) => {
				if (this.hasAttribute(attr)) {
					input.setAttribute(attr, this.getAttribute(attr));
					this.removeAttribute(attr);
				}
			});
		});
	}
}

customElements.define('am-field', FieldComponent);