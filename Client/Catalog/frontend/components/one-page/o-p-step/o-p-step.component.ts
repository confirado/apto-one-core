import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ProgressElement, ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-o-p-step',
	templateUrl: './o-p-step.component.html',
	styleUrls: ['./o-p-step.component.scss'],
})
export class OPStepComponent implements OnInit {
	@Input()
	public section: ProgressStep | undefined;

	@Input()
	public index: number | undefined;

	@Input()
	public status: string | undefined;

	@Input()
	public description: string | undefined;

	@Input()
	public last: boolean | undefined;

	@Input()
	public product: Product | null | undefined;

	@Input()
	public elements: ProgressElement[] | undefined | null;

	public constructor(private store: Store, public matDialog: MatDialog) {}

	public panelOpenState: boolean = false;

	public ngOnInit(): void {}

	public togglePanel(): void {
		this.panelOpenState = !this.panelOpenState;
	}
}
