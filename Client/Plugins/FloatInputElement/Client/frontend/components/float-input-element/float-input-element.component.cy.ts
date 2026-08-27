// float-input-element.component.cy.ts

import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { FloatInputElementComponent } from '@element-definition-float-input-element';
import { AptoFloatInputElementFrontendModule } from '@apto-float-input-element-frontend/apto-float-input-element-frontend.module';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { FloatInputTypes, Product, Section } from '@apto-catalog-frontend-product-model';

import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

interface DispatchSpy {
  getCalls(): Array<{ args: [{ type: string }] }>;
}

describe('FloatInputElementComponent', () => {
  it('dispatches updateConfigurationState with the entered value when Apply is clicked', () => {
    // Arrange: Fixture liefert das ProgressElement, wir setzen nur, was fuer diesen Testfall zaehlt
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          staticValues: { renderingType: FloatInputTypes.INPUT },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(FloatInputElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoFloatInputElementFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [
        provideMockStore({ initialState: buildMockStoreInitialState() }),
        { provide: DialogService, useValue: {} },
      ],
    }).then(() => {
      const store = TestBed.inject(MockStore);
      cy.spy(store, 'dispatch').as('dispatchSpy');
    });

    cy.clock();
    cy.get('[data-cy="input-field-numeric"]').clear().type('15');

    cy.get('[data-cy="input-field-numeric"]').should('have.value', '15');
    cy.tick(100);

    cy.get('[data-cy="apply-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').should((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 0,
            property: 'value',
            value: 15,
          }],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });
});
