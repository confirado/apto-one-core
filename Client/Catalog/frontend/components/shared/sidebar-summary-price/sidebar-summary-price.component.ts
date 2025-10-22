import { Component, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FormControl, FormGroup } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Actions, ofType } from "@ngrx/effects";
import { take } from "rxjs";
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { configurationIsValid, selectCurrentPerspective } from '@apto-catalog-frontend-configuration-selectors';
import {addToBasket, addToBasketSuccess} from '@apto-catalog-frontend-configuration-actions';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';

@UntilDestroy()
@Component({
	selector: 'apto-sidebar-summary-price',
	templateUrl: './sidebar-summary-price.component.html',
	styleUrls: ['./sidebar-summary-price.component.scss'],
})
export class SidebarSummaryPriceComponent {
	@Input()
	public sumPrice: string | null | undefined;
	@Input()
	public progress: number | null | undefined;
	@Input()
	public product: Product | null | undefined;
	@Input()
	public sumPseudoPrice: string | null | undefined;
	@Input()
	public discount: number | undefined;

  public renderImage = null;
  public sw6CartButtonDisabled: boolean = false;

  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });

	public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('aptoStepByStep.sidebarSummary'));
  public readonly configurationIsValid$ = this.store.select(configurationIsValid);
  public readonly isInline = !!environment.aptoInline;

	public constructor(
    private store: Store,
    private renderImageService: RenderImageService,
    private activatedRoute: ActivatedRoute,
    private readonly actions$: Actions
  ) {
    this.store.select(selectCurrentPerspective).pipe(untilDestroyed(this)).subscribe(async (result: string) => {
      this.renderImage = await this.renderImageService.drawImageForPerspective(result);
    });
  }

  private get configurationId(): string {
    return this.activatedRoute.snapshot.params['configurationId'];
  }

  private get configurationType(): string {
    return this.activatedRoute.snapshot.params['configurationType'];
  }

  protected get showPrices(): boolean {
    return environment.showPrices;
  }

  public openShopware6Cart() {
    this.sw6CartButtonDisabled = true;
    this.actions$.pipe(
      ofType(addToBasketSuccess),
      untilDestroyed(this),
      take(1)
    ).subscribe((next) => {
      const offCanvasCartInstances: any = window.PluginManager.getPluginInstances('OffCanvasCart');
      for (let i = 0; i < offCanvasCartInstances.length; i++) {
        offCanvasCartInstances[i].openOffCanvas(window.router['frontend.cart.offcanvas'], false);
      }
      this.sw6CartButtonDisabled = false;
    });

    if (this.renderImage) {
      this.renderImageService.resize(this.renderImage, 800).then((image: any) => {
        this.store.dispatch(
          addToBasket({
            payload: {
              type: 'ADD_TO_BASKET',
              productImage: image.src,
              configurationId: this.configurationId,
              configurationType: this.configurationType,
            },
          })
        );
      });
    } else {
      this.store.dispatch(
        addToBasket({
          payload: {
            type: 'ADD_TO_BASKET',
            productImage: null,
            configurationId: this.configurationId,
            configurationType: this.configurationType,
          },
        })
      );
    }
  }
}
