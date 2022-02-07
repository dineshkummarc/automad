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
	getPageURL,
	getTagFromRoute,
	html,
	Routes,
} from '../../core';
import { SidebarLayoutComponent } from './SidebarLayout';

/**
 * The page view.
 *
 * @extends SidebarLayoutComponent
 */
export class PageComponent extends SidebarLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return getPageURL();
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		let more = '';
		let modal = '';

		if (getPageURL() !== '/') {
			more = html`
				<am-dropdown class="am-c-dropdown">
					$${App.text('btn_more')}
					<div class="am-c-dropdown__items">
						<div class="am-c-dropdown__item">
							<i class="bi bi-pencil"></i>
							<a
								href="${App.baseURL}${getPageURL()}"
								target="_blank"
							>
								$${App.text('btn_inpage_edit')}
							</a>
						</div>
						<div class="am-c-dropdown__item">
							<am-form api="Page/duplicate">
								<i class="bi bi-files"></i>
								<am-submit
									>$${App.text(
										'btn_duplicate_page'
									)}</am-submit
								>
							</am-form>
						</div>
						<div class="am-c-dropdown__item">
							<am-modal-toggle modal="#am-move-page-modal">
								<i class="bi bi-arrows-move"></i>
								$${App.text('btn_move_page')}
							</am-modal-toggle>
						</div>
						<div class="am-c-dropdown__item">
							<am-form
								api="Page/delete"
								confirm="$${App.text('confirm_delete_page')}"
							>
								<i class="bi bi-trash2"></i>
								<am-submit
									>$${App.text('btn_delete_page')}</am-submit
								>
							</am-form>
						</div>
						<div class="am-c-dropdown__item">
							<i class="bi bi-clipboard-plus"></i>
							<am-copy value="${getPageURL()}">
								$${App.text('btn_copy_url_clipboard')}
							</am-copy>
						</div>
					</div>
				</am-dropdown>
			`;

			modal = html`
				<am-modal id="am-move-page-modal">
					<div class="am-c-modal__dialog">
						<div class="am-c-modal__header">
							<span>$${App.text('btn_move_page')}</span>
							<am-modal-close
								class="${classes.modalClose}"
							></am-modal-close>
						</div>
						<am-form api="Page/move">
							<div class="am-c-field">
								<label class="am-c-field__label">
									${App.text('page_move_destination')}
								</label>
								<am-page-select-tree
									hidecurrent
								></am-page-select-tree>
							</div>

							<am-submit
								class="${classes.button} ${classes.buttonSuccess}"
							>
								$${App.text('btn_move_page')}
								<i class="bi bi-arrows-move"></i>
							</am-submit>
						</am-form>
					</div>
				</am-modal>
			`;
		}

		return html`
			<section class="am-l-main__row">
				<nav class="am-l-main__content">
					<am-page-breadcrumbs></am-page-breadcrumbs>
				</nav>
			</section>
			<section class="am-l-main__row am-l-main__row--sticky">
				<menu class="am-l-main__content">
					<div class="am-u-flex">
						<am-switcher class="am-u-flex__item-grow">
							<am-switcher-link
								section="${App.sections.content.settings}"
							>
								$${App.text('page_settings')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.text}"
							>
								$${App.text('page_vars_content')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.colors}"
							>
								$${App.text('page_vars_color')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.files}"
							>
								$${App.text('btn_files')}
								<am-file-count></am-file-count>
							</am-switcher-link>
						</am-switcher>
						${more}
					</div>
				</menu>
			</section>
			<section class="am-l-main__row">
				<div class="am-l-main__content">
					<am-page-data api="Page/data" watch></am-page-data>
					<am-switcher-section name="${App.sections.content.files}">
						<am-submit form="FileCollection/list"
							>$${App.text('btn_remove_selected')}</am-submit
						>
						<am-file-collection-list
							confirm="$${App.text('confirm_delete_files')}"
							api="FileCollection/list"
							watch
						></am-file-collection-list>
					</am-switcher-section>
				</div>
			</section>
			${modal}
		`;
	}

	/**
	 * Render the save button partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderSaveButtonPartial(): string {
		return html`<am-submit form="Page/data">
			${App.text('btn_save')}
		</am-submit>`;
	}
}

customElements.define(getTagFromRoute(Routes[Routes.page]), PageComponent);
