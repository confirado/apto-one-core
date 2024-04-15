import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { combineLatest } from "rxjs";
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { RenderImageService } from "@apto-catalog-frontend/services/render-image.service";
import { RenderImageData } from "@apto-catalog-frontend/store/configuration/configuration.model";
import {
  selectConfiguration,
  selectCurrentPerspective,
  selectCurrentRenderImages,
  selectPerspectives,
  selectProgress,
  selectSumPrice,
  selectSumPseudoPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import {
  createLoadingFlagAction,
  hideLoadingFlagAction
} from "@apto-catalog-frontend/store/configuration/configuration.actions";

@UntilDestroy()
@Component({
	selector: 'apto-sidebar-summary',
	templateUrl: './sidebar-summary.component.html',
	styleUrls: ['./sidebar-summary.component.scss'],
})
export class SidebarSummaryComponent implements OnInit {
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
  public renderImage = null;

  public constructor(private store: Store, private renderImageService: RenderImageService) {
    combineLatest([
      this.store.select(selectCurrentPerspective),
      this.store.select(selectCurrentRenderImages)
    ]).pipe(untilDestroyed(this)).subscribe(async (result: [string, RenderImageData[]]) => {
      // why is step-by-step not working when dispatch loading flag action/actions
      //this.store.dispatch(createLoadingFlagAction());

      // is it necessary to reset renderImage to null? reset to null causes a unpleasant flickering
      //this.renderImage = null;

      this.renderImage = await this.renderImageService.drawImageForPerspective(result[0]);

      // why is step-by-step not working when dispatch loading flag action/actions
      //this.store.dispatch(hideLoadingFlagAction());
    });
  }

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
