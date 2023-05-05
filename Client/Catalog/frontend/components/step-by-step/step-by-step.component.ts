import { Component } from '@angular/core';
import { TemplateSlot } from '@apto-base-core/template-slot/template-slot.decorator';
import { selectConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-step-by-step',
	templateUrl: './step-by-step.component.html',
	styleUrls: ['./step-by-step.component.scss'],
})
@TemplateSlot({
	slot: 'frontend-content',
})
export class StepByStepComponent {
	public configuration$ = this.store.select(selectConfiguration);

	public constructor(private store: Store) {}
}
