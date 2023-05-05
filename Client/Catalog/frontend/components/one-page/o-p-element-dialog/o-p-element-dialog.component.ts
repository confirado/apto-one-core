import { Component, Input } from '@angular/core';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

@Component({
	selector: 'apto-o-p-element-dialog',
	templateUrl: './o-p-element-dialog.component.html',
	styleUrls: ['./o-p-element-dialog.component.scss'],
})
export class OPElementDialogComponent {
	@Input()
	public element: ProgressElement | undefined;

	@Input()
	public product: Product | null | undefined;
}
