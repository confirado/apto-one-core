import { Component, Input, OnInit } from '@angular/core';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState, ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { selectElementValues } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Element, Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';

@Component({
	selector: 'apto-sbs-step',
	templateUrl: './sbs-step.component.html',
	styleUrls: ['./sbs-step.component.scss'],
})
export class SbsStepComponent implements OnInit {
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

	@Input()
	public active: boolean | undefined;

	@Input()
	public state: ProgressState | undefined;

	public constructor(private store: Store) {}

	public opened(id: string, sectionList: string[]): boolean {
		return sectionList.includes(id);
	}

	public getElementValues(element: Element): Observable<TranslatedValue[] | null | undefined> {
		return this.store.select(selectElementValues(element));
	}

	public setStep(section: ProgressStep | undefined): void {
		// eslint-disable-next-line no-restricted-globals
		if (section && !this.state?.afterSteps.includes(section)) {
			scrollTo(0, 0);
			this.store.dispatch(
				setStep({
					payload: {
						id: section.section.id,
					},
				})
			);
		}
	}

	public panelOpenState: boolean = false;

	public isActive: boolean = false;

	public ngOnInit(): void {}

	public togglePanel(): void {
		this.panelOpenState = !this.panelOpenState;
	}

	public isActiveSection(): void {
		this.isActive = true;
	}
}
