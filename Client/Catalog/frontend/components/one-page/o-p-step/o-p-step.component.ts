import { Component, Input, OnInit, SimpleChanges } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';

import { Product } from '@apto-catalog-frontend/store/product/product.model';
import {
    ElementState,
    ProgressElement, ProgressStatuses,
    ProgressStep
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
    selectSectionProductElements,
    selectSectionStateElements
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
    selector: 'apto-o-p-step',
    templateUrl: './o-p-step.component.html',
    styleUrls: ['./o-p-step.component.scss']
})
export class OPStepComponent implements OnInit {
    @Input()
    public section: ProgressStep | undefined;

    @Input()
    public index: number | undefined;

    @Input()
    public status: ProgressStatuses | undefined;

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
    public previewImageLink?: string;

    private sectionStateElements: ElementState[] = null;

    public constructor(
        private readonly store: Store,
        public readonly matDialog: MatDialog
    ) {
    }

    public ngOnInit(): void {
        this.store.select(selectSectionProductElements(this.section.section.id)).subscribe((next) => {
            this.sectionProductElements = next;
        });

        this.store.select(selectSectionStateElements(this.section.section.id)).subscribe((next) => {
            this.sectionStateElements = next;
        });

        this.previewImageLink = this.section?.elements.filter(el => el.state.active)[0]?.element.previewImage;
    }

    public ngOnChanges(changes: SimpleChanges): void {
        this.previewImageLink = changes['section']?.currentValue.elements.filter(el => el.state.active)[0]?.element.previewImage;
    }

    public isElementDisabled(elementId: string, sectionRepetition: number): boolean {
        const state = this.sectionStateElements.filter(e => e.id === elementId);
        return state.length > 0 ? state[sectionRepetition].disabled : false;
    }

    public getProgressElement(elementId: string): ProgressElement | null {
        const element = this.elements.filter(e => e.element.id === elementId);
        return element.length > 0 ? element[0] : null;
    }

    public togglePanel(): void {
        this.panelOpenState = !this.panelOpenState;
    }
}
