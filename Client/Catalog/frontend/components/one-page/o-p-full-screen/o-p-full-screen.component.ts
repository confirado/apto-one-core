import { Component, Input } from '@angular/core';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-o-p-full-screen',
	templateUrl: './o-p-full-screen.component.html',
	styleUrls: ['./o-p-full-screen.component.scss'],
})
export class OPFullScreenComponent {
	@Input()
	public product: Product | null | undefined;

	@Input()
	public renderImage: RenderImage | undefined | null;

	@Input()
	public perspectives: string[] | undefined | null;

	public isOpen = false;

	public constructor(private store: Store) {}

	public prevRenderImage(): void {
		this.store.dispatch(setPrevPerspective());
	}

	public nextRenderImage(): void {
		this.store.dispatch(setNextPerspective());
	}
}
