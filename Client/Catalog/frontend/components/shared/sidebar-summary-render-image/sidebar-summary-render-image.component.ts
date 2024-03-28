import { Component, Input, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { UntilDestroy } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
	selector: 'apto-sidebar-summary-render-image',
	templateUrl: './sidebar-summary-render-image.component.html',
	styleUrls: ['./sidebar-summary-render-image.component.scss'],
})
export class SidebarSummaryRenderImageComponent {
	@Input()
	public perspectives: string[] | undefined | null;

	@Input()
	public product: Product | null | undefined;

  @Input()
  public renderImage = null;

	public constructor(private store: Store) {
  }

	public prevRenderImage(): void {
		this.store.dispatch(setPrevPerspective());
	}

	public nextRenderImage(): void {
		this.store.dispatch(setNextPerspective());
	}
}
