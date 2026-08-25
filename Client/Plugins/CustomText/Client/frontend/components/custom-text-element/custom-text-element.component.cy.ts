import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { Action, StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { CustomTextElementComponent } from '@element-definition-custom-text-element';
import { AptoCustomTextFrontendModule } from '@apto-custom-text-frontend/apto-custom-text-frontend.module';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { ElementState } from '@apto-catalog-frontend-configuration-model';
import { Product, Section } from '@apto-catalog-frontend-product-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

interface DispatchSpy {
  getCalls(): Array<{ args: [Action] }>;
}

function mountCustomText(rendering: 'input' | 'textarea', stateOverrides: Partial<ElementState> = {}): Cypress.Chainable {
  const element = buildProgressElement({
    id: 'element-42',
    sectionId: 'section-7',
    state: { sectionRepetition: 2, ...stateOverrides },
    element: {
      definition: {
        properties: { text: [{ minLength: 2, maxLength: 12 }] },
        staticValues: { rendering },
      },
    },
  });

  return mount(CustomTextElementComponent, {
    componentProperties: {
      element,
      product: { useStepByStep: true } as Product,
      section: { allowMultiple: false } as Section,
    },
    imports: [AptoCustomTextFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
    providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
  }).then(() => {
    cy.spy(TestBed.inject(MockStore), 'dispatch').as('dispatchSpy');
  });
}

function expectOnlyConfigurationAction(expected: ReturnType<typeof updateConfigurationState>): void {
  cy.get<DispatchSpy>('@dispatchSpy').then((dispatchSpy) => {
    const actions = dispatchSpy.getCalls().map((call) => call.args[0]).filter((action) => action.type === expected.type);
    expect(actions).to.deep.equal([expected]);
  });
}

describe('CustomTextElementComponent', () => {
  it('initializes and saves a valid input value', () => {
    mountCustomText('input', { values: { text: 'Initial' } });

    cy.clock();
    cy.get('[data-cy="custom-text-input"] input').should('have.value', 'Initial').clear().type('Custom text');
    cy.get('[data-cy="custom-text-input"] input').should('have.value', 'Custom text');
    cy.clock().tick(100);
    cy.get('[data-cy="custom-text-apply-button"]').click();

    expectOnlyConfigurationAction(updateConfigurationState({
      updates: {
        set: [
          {
            sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 2,
            property: 'aptoElementDefinitionId', value: 'apto-element-custom-text',
          },
          { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 2, property: 'text', value: 'Custom text' },
        ],
      },
    }));
  });

  it('renders the textarea variant and removes an active value', () => {
    mountCustomText('textarea', { active: true, values: { text: 'Existing value' } });

    cy.get('[data-cy="custom-text-textarea"] textarea').should('have.value', 'Existing value');
    cy.get('[data-cy="custom-text-remove-button"]').click();

    expectOnlyConfigurationAction(updateConfigurationState({
      updates: {
        remove: [{
          sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 2,
          property: 'aptoElementDefinitionId', value: 'apto-element-custom-text',
        }],
      },
    }));
  });
});
