import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';

import { Product } from '@apto-catalog-frontend/store/product/product.model';
import {
  ElementState,
  ProgressElement,
  ProgressStep
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  selectSectionProductElements,
  selectSectionStateElements
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';

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

  public panelOpenState: boolean = false;
  public sectionProductElements = [];

  private sectionStateElements: ElementState[] = null;

	public constructor(private store: Store, public matDialog: MatDialog) {}

	public ngOnInit(): void {
    this.store.select(selectSectionProductElements(this.section.section.id)).subscribe((next) => {
      this.sectionProductElements = next;
    })

    this.store.select(selectSectionStateElements(this.section.section.id)).subscribe((next) => {
      this.sectionStateElements = next;
    })
  }

  public isElementDisabled(elementId: string) {
    const state = this.sectionStateElements.filter(e => e.id === elementId);
    if (state.length > 0) {
      return state[0].disabled;
    }
    return false;
  }

  public getProgressElement(elementId: string): ProgressElement | null {
    const element = this.elements.filter(e => e.element.id === elementId);
    if (element.length > 0) {
      return element[0];
    }
    return null;
  }

	public togglePanel(): void {
		this.panelOpenState = !this.panelOpenState;
	}
}
