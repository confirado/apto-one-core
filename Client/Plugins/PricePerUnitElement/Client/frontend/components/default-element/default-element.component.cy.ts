import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { Action } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { DefaultElementComponent } from '@element-definition-price-per-unit-default-element';
import { AptoPricePerUnitElementFrontendModule } from '@apto-price-per-unit-element-frontend/apto-price-per-unit-element-frontend.module';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { Product, Section } from '@apto-catalog-frontend-product-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

interface DispatchSpy {
  getCalls(): Array<{ args: [Action] }>;
}

function mountDefaultElement(product: Partial<Product>, active: boolean) {
  const progressElement = buildProgressElement({
    state: {
      id: 'element-42',
      sectionId: 'section-7',
      sectionRepetition: 3,
      active,
    },
    element: {
      id: 'element-42',
      sectionId: 'section-7',
      name: { de_DE: 'Price per unit' },
    },
  });

  return mount(DefaultElementComponent, {
    componentProperties: {
      element: progressElement,
      product: product as Product,
      section: { allowMultiple: false } as Section,
    },
    imports: [
      AptoPricePerUnitElementFrontendModule,
      StoreModule.forRoot({}),
      EffectsModule.forRoot([]),
    ],
    providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
  }).then(() => {
    const store = TestBed.inject(MockStore);
    cy.spy(store, 'dispatch').as('dispatchSpy');
  });
}

describe('DefaultElementComponent (PricePerUnit)', () => {
  it('dispatches a set update from the step-by-step representation', () => {
    mountDefaultElement({ useStepByStep: true }, false);

    cy.get('[data-cy="price-per-unit-step-by-step"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const action = spy.getCalls().map((call) => call.args[0]).find((candidate) => candidate.type === updateConfigurationState.type);
      expect(action).to.deep.equal(updateConfigurationState({
        updates: {
          set: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 3,
            property: null,
            value: null,
          }],
        },
      }));
    });
  });

  it('dispatches a remove update from the one-page representation', () => {
    mountDefaultElement({ useStepByStep: false }, true);

    cy.get('[data-cy="price-per-unit-one-page"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const action = spy.getCalls().map((call) => call.args[0]).find((candidate) => candidate.type === updateConfigurationState.type);
      expect(action).to.deep.equal(updateConfigurationState({
        updates: {
          remove: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 3,
            property: null,
            value: null,
          }],
        },
      }));
    });
  });
});
