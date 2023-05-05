import { Component, Input } from '@angular/core';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';

@Component({
	selector: 'apto-default-element-step-by-step',
	templateUrl: './default-element-step-by-step.component.html',
	styleUrls: ['./default-element-step-by-step.component.scss'],
})
export class DefaultElementStepByStepComponent {
	@Input()
	public element: ProgressElement | undefined;
	@Input()
	public section: Section | undefined;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;
}
