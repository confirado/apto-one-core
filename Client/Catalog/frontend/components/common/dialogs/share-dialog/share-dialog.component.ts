import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { combineLatest, map } from 'rxjs';

@Component({
	selector: 'apto-share-dialog',
	templateUrl: './share-dialog.component.html',
	styleUrls: ['./share-dialog.component.scss'],
})
export class ShareDialogComponent {
	public readonly links$ = combineLatest([
		this.store.select(selectContentSnippet('aptoSliderAction.shareLinks')),
		this.store.select(selectLocale),
		this.store.select(selectProduct),
	]).pipe(
		map(([shareLinks, locale, product]) => {
			if (!shareLinks || !locale || !shareLinks.children) {
				return [];
			}

			const currentURL = encodeURIComponent(window.location.href);
			const links = [];
			for (const link of shareLinks.children) {
				if (link.children) {
					links.push({
						iconClass: link.children.find((c) => c.name === 'iconClass')?.content?.[locale],
						link: link.children
							.find((c) => c.name === 'link')
							?.content?.[locale]?.replace('{_currentUrl_}', currentURL)
							.replace('{_productName_}', product?.name?.[locale] || ''),
						target: link.children.find((c) => c.name === 'target')?.content?.[locale],
					});
				}
			}

			return links;
		})
	);

	public constructor(private store: Store) {}
}
