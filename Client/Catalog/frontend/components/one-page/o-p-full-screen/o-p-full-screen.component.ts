import { Component, Input } from '@angular/core';
import { Store } from '@ngrx/store';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend-configuration-actions';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

@Component({
	selector: 'apto-o-p-full-screen',
	templateUrl: './o-p-full-screen.component.html',
	styleUrls: ['./o-p-full-screen.component.scss'],
})
export class OPFullScreenComponent {
	@Input()
	public product: Product | null | undefined;

	@Input()
	public perspectives: string[] | undefined | null;

  @Input()
  public renderImage = null;

  @Input()
  public imageWrapperBgColor = 'transparent';

	public isOpen = false;

	public constructor(private store: Store) {
  }

	public prevRenderImage(): void {
		this.store.dispatch(setPrevPerspective());
	}

	public nextRenderImage(): void {
		this.store.dispatch(setNextPerspective());
	}
}
