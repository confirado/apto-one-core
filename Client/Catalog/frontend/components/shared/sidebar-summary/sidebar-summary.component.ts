import { Component, OnInit } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import {
	selectConfiguration,
	selectPerspectives,
	selectProgress,
	selectRenderImage,
	selectSumPrice,
	selectSumPseudoPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';

@Component({
	selector: 'apto-sidebar-summary',
	templateUrl: './sidebar-summary.component.html',
	styleUrls: ['./sidebar-summary.component.scss'],
})
export class SidebarSummaryComponent implements OnInit {
	public readonly renderImage$ = this.store.select(selectRenderImage);
	public readonly perspectives$ = this.store.select(selectPerspectives);
	public readonly sumPrice$ = this.store.select(selectSumPrice);
	public readonly progress$ = this.store.select(selectProgress);
	public readonly product$ = this.store.select(selectProduct);
	public readonly sumPseudoPrice$ = this.store.select(selectSumPseudoPrice);
	public readonly configuration$ = this.store.select(selectConfiguration);
	public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoSummary'));

  protected readonly AptoOfferConfigurationDialog$ = this.store.select(selectContentSnippet('AptoOfferConfigurationDialog'));
  protected readonly locale$ = this.store.select(selectLocale);
  private locale = 'de_DE';
  protected isOfferConfigurationEnabled = false;
	public constructor(private store: Store) {}

	public ngOnInit(): void {
    this.locale$.subscribe((next) => {
      this.locale = next;
    });

    this.AptoOfferConfigurationDialog$.subscribe((data) => {
      data.children.forEach((dialogItem) => {
        if (dialogItem.name === 'enabled') {
          this.isOfferConfigurationEnabled = dialogItem.content[this.locale] === 'true';
        }
      });
    });
  }

	public openShareDialog(): void {}
}
