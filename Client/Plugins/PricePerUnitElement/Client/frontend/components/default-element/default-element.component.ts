import { Component, Input } from '@angular/core';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';

@Component({
    selector: 'apto-default-element',
    templateUrl: './default-element.component.html',
    styleUrls: ['./default-element.component.scss']
})
export class DefaultElementComponent {
    @Input()
    public product: Product | null | undefined;

    @Input()
    public section: Section | undefined;

    @Input()
    public element: ProgressElement | undefined | null;

    public constructor(private readonly store: Store) {}

    public clickOnElement(active: boolean): void {
        if (active) {
            this.removeElement();
        } else {
            this.selectElement();
        }
    }

    public selectElement(): void {
        if (!this.element) {
            return;
        }
        this.store.dispatch(
            updateConfigurationState({
                updates: {
                    set: [
                        {
                            sectionId: this.element!.element.sectionId,
                            elementId: this.element!.element.id,
                            sectionRepetition: this.element!.state.sectionRepetition,
                            property: null,
                            value: null
                        }
                    ]
                }
            })
        );
    }

    public removeElement(): void {
        if (!this.element) {
            return;
        }
        this.store.dispatch(
            updateConfigurationState({
                updates: {
                    remove: [
                        {
                            sectionId: this.element!.element.sectionId,
                            elementId: this.element!.element.id,
                            sectionRepetition: this.element!.state.sectionRepetition,
                            property: null,
                            value: null
                        }
                    ]
                }
            })
        );
    }
}
