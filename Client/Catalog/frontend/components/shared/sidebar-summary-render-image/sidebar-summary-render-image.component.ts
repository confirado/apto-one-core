import { Component, Input } from '@angular/core';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-sidebar-summary-render-image',
	templateUrl: './sidebar-summary-render-image.component.html',
	styleUrls: ['./sidebar-summary-render-image.component.scss'],
})
export class SidebarSummaryRenderImageComponent {
	@Input()
	public renderImage: RenderImage | undefined | null;

	@Input()
	public perspectives: string[] | undefined | null;

	@Input()
	public product: Product | null | undefined;

	public constructor(private store: Store) {}

	public prevRenderImage(): void {
		this.store.dispatch(setPrevPerspective());
	}

	public nextRenderImage(): void {
		this.store.dispatch(setNextPerspective());
	}
}
