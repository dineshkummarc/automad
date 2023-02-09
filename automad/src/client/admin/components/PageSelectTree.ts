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

import SortableTree, {
	SortableTreeKeyValue,
	SortableTreeRenderLabelFunction,
} from 'sortable-tree';
import {
	App,
	Attr,
	CSS,
	EventName,
	getPageURL,
	html,
	listen,
	query,
} from '../core';
import { createSortableTreeNodes, treeStyles } from '../core/tree';
import { PageMetaData } from '../types';
import { BaseComponent } from './Base';

/**
 * The render function that renders the label HTML.
 *
 * @param data
 * @returns the rendered HTML
 */
const renderLabelFunction: SortableTreeRenderLabelFunction = (
	data: SortableTreeKeyValue
): string => {
	const icon = data.private ? 'eye-slash-fill' : 'folder2';

	return html`
		<label class="${CSS.navItem}">
			<input
				class="${CSS.displayNone}"
				type="radio"
				name="targetPage"
				value="${data.url}"
			/>
			<span class="${CSS.navLink}">
				<am-icon-text
					${Attr.icon}="${icon}"
					${Attr.text}="$${data.title}"
				></am-icon-text>
			</span>
		</label>
	`;
};

/**
 * An page selection tree field.
 *
 * @example
 * <am-page-select ${Attr.hideCurrent}></am-page-select>
 *
 * @extends BaseComponent
 */
class PageSelectTreeComponent extends BaseComponent {
	/**
	 * True if the current page should be excluded.
	 */
	private get hideCurrent(): boolean {
		return this.hasAttribute(Attr.hideCurrent);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listeners.push(
			listen(window, EventName.appStateChange, this.init.bind(this))
		);

		this.init();
	}

	/**
	 * Init the navTree.
	 */
	private init(): void {
		this.innerHTML = '';

		const pages: PageMetaData[] = this.filterPages(
			Object.values(App.pages) as PageMetaData[]
		);

		const nodes = createSortableTreeNodes(pages);

		const tree = new SortableTree({
			nodes,
			element: this,
			initCollapseLevel: 1,
			disableSorting: true,
			styles: treeStyles,
			renderLabel: renderLabelFunction,
		});

		const currentNode =
			tree.findNode('url', getPageURL()) || tree.findNode('url', '/');

		currentNode.reveal();

		(query('input', currentNode.label) as HTMLInputElement).checked = true;
	}

	/**
	 * Optionally define a filter function for the pages array.
	 *
	 * @param pages
	 * @returns the array of filtered pages
	 */
	private filterPages(pages: PageMetaData[]): PageMetaData[] {
		if (this.hideCurrent) {
			const current = getPageURL();
			const regex = new RegExp(`^${current}(\/|$)`);

			return pages.filter((page: PageMetaData) => {
				return page.url.match(regex) == null;
			});
		}

		return pages;
	}
}

customElements.define('am-page-select-tree', PageSelectTreeComponent);
