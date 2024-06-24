import { Component, Input } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { select, Store } from '@ngrx/store';
import { FormControl, FormGroup } from '@angular/forms';
import {
  configurationIsValid,
  selectConfiguration,
  selectCurrentPerspective
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { addToBasket } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { ActivatedRoute, Router } from '@angular/router';
import {UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

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

  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });

  public configuration$ = this.store.select(selectConfiguration);

	public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('aptoStepByStep.sidebarSummary'));
  public readonly configurationIsValid$ = this.store.select(configurationIsValid);
  public readonly isInline = !!environment.aptoInline;

	public constructor(
    private store: Store,
    private renderImageService: RenderImageService,
    private router: Router,
    private activatedRoute: ActivatedRoute)
  {
    this.configuration$.subscribe((c) => {
      if (!c.loading && c.state.sections.length === 0) {
        router.navigate(['..'], { relativeTo: activatedRoute });
      }
    });

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

    const offCanvasCartInstances: any = window.PluginManager.getPluginInstances('OffCanvasCart');
    for (let i = 0; i < offCanvasCartInstances.length; i++) {
      offCanvasCartInstances[i].openOffCanvas(window.router['frontend.cart.offcanvas'], false);
    }
  }
}
