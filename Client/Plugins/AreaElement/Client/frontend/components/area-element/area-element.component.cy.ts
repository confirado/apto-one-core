import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { AreaElementComponent } from '@element-definition-area-element';
import { AptoAreaElementFrontendModule } from '@apto-area-element-frontend/apto-area-element-frontend.module';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { InfoField, Product, Section } from '@apto-catalog-frontend-product-model';
import { AreaElementDefinitionProperties } from '@apto-catalog-frontend-configuration-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const inputProperties = {
  field_0: [{ type: 'range', minimum: 0, maximum: 20, step: 1 }],
} as unknown as AreaElementDefinitionProperties;

const inputFields: InfoField[] = [{ rendering: 'input', default: 1, prefix: { de_DE: '' }, suffix: { de_DE: '' } }];

interface DispatchSpy {
  getCalls(): Array<{ args: [{ type: string }] }>;
}

describe('AreaElementComponent', () => {
  it('dispatches the entered area field values on submit', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: { field_0: 3 },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: inputProperties,
          staticValues: { fields: inputFields },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(AreaElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoAreaElementFrontendModule,
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
    cy.get('[data-cy="area-field-field_0"] input').clear().type('7').should('have.value', '7');
    cy.tick(100);
    cy.get('[data-cy="area-apply-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 0,
            property: 'field_0',
            value: 7,
          }],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('saves automatically when all area fields are selects', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: { field_0: '1', field_1: '2' },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: {
            field_0: [{ type: 'range', minimum: 1, maximum: 3, step: 1 }],
            field_1: [{ type: 'range', minimum: 1, maximum: 3, step: 1 }],
          } as unknown as AreaElementDefinitionProperties,
          staticValues: {
            fields: [
              { rendering: 'select', default: 1, prefix: { de_DE: '' }, suffix: { de_DE: '' } },
              { rendering: 'select', default: 2, prefix: { de_DE: '' }, suffix: { de_DE: '' } },
            ],
          },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(AreaElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoAreaElementFrontendModule,
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

    cy.get('[data-cy="area-field-field_0"] [data-cy="select-field-trigger"]').click();
    cy.get('[data-cy="select-item-3"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'field_0', value: '3' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'field_1', value: '2' },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('dispatches remove updates for the active area fields', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 1,
        active: true,
        values: { field_0: 3 },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: inputProperties,
          staticValues: { fields: inputFields },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(AreaElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoAreaElementFrontendModule,
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

    cy.get('[data-cy="area-remove-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          remove: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 1,
            property: 'field_0',
            value: 3,
          }],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });
});
