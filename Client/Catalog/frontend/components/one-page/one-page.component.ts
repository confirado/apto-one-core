import { Component, SimpleChanges } from '@angular/core';
import { Store } from '@ngrx/store';
import { TemplateSlot } from '@apto-base-core/template-slot/template-slot.decorator';
import { TemplateSlotInterface } from '@apto-base-core/template-slot/template-slot.interface';
import {
  selectConfigurationLoading, selectCurrentPerspective,
  selectHideOnePage,
  selectPerspectives,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { combineLatest } from 'rxjs';
import {
  createLoadingFlagAction,
  hideLoadingFlagAction,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';

@UntilDestroy()
@Component({
	selector: 'apto-one-page',
	templateUrl: './one-page.component.html',
	styleUrls: ['./one-page.component.scss'],
})
@TemplateSlot({
	slot: 'frontend-content',
})
export class OnePageComponent implements TemplateSlotInterface {
  public readonly product$ = this.store.select(selectProduct);
	public readonly perspectives$ = this.store.select(selectPerspectives);
  public readonly configurationLoading$ = this.store.select(selectConfigurationLoading);
  protected readonly loadingIndicatorTypes = LoadingIndicatorTypes;
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoOnePage'));
  public hideOnePage: boolean = false;

  public renderImage = null;

	public constructor(private store: Store, private renderImageService: RenderImageService) {
    combineLatest([
      this.store.select(selectHideOnePage),
      this.store.select(selectCurrentPerspective),
    ]).pipe(untilDestroyed(this)).subscribe(async (result: [boolean, string]) => {
      this.hideOnePage = result[0];

      //render only for not designer view
      if (!result[0]) {
        this.store.dispatch(createLoadingFlagAction());
        this.renderImage = null;
        this.renderImage = await this.renderImageService.drawImageForPerspective(result[1]);
        this.store.dispatch(hideLoadingFlagAction());
      }
    });
  }

  onPropsChanged(changes: SimpleChanges): void {}
}
