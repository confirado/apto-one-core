import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';
import { of } from 'rxjs';

import { SelectboxElementComponent } from '@element-definition-selectbox-element';
import { AptoSelectBoxElementFrontendModule } from '@apto-select-box-element-frontend/apto-select-box-element-frontend.module';
import { CatalogMessageBusService } from '@apto-catalog-frontend-service-catalog-message-bus';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { Product, Section } from '@apto-catalog-frontend-product-model';

import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const mockMessageBus = {
  provide: CatalogMessageBusService,
  useValue: {
    findSelectBoxItems: () => of({
      data: [
        { id: 'item-1', surrogateId: 's-1', name: { de_DE: 'Option 1' }, isDefault: false, aptoPrices: [] },
        { id: 'item-2', surrogateId: 's-2', name: { de_DE: 'Option 2' }, isDefault: false, aptoPrices: [] },
      ],
    }),
  },
};

interface DispatchSpy {
  getCalls(): Array<{ args: [{ type: string }] }>;
}

describe('SelectboxElementComponent', () => {

  it('dispatches updateConfigurationState when selecting an item (single select)', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        active: true,
        values: { boxes: [] },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          staticValues: { enableMultiSelect: false },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(SelectboxElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoSelectBoxElementFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [
        provideMockStore({ initialState: buildMockStoreInitialState() }),
        mockMessageBus,
      ],
    }).then(() => {
      const store = TestBed.inject(MockStore);
      cy.spy(store, 'dispatch').as('dispatchSpy');
    });

    cy.get('[data-cy="select-field-trigger"]').click();
    cy.get('[data-cy="select-item-item-1"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'aptoElementDefinitionId', value: 'apto-element-select-box' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'boxes', value: [{ id: 'item-1', name: { de_DE: 'Option 1' }, multi: '1' }] },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'selectedItem', value: ['item-1'] },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('dispatches updateConfigurationState when selecting an item (multi select)', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        active: true,
        values: { boxes: [] },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          staticValues: { enableMultiSelect: true },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(SelectboxElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoSelectBoxElementFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [
        provideMockStore({ initialState: buildMockStoreInitialState() }),
        mockMessageBus,
      ],
    }).then(() => {
      const store = TestBed.inject(MockStore);
      cy.spy(store, 'dispatch').as('dispatchSpy');
    });

    cy.get('[data-cy="select-box-field-trigger"]').click();
    cy.get('[data-cy="select-box-item-item-1"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'aptoElementDefinitionId', value: 'apto-element-select-box' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'boxes', value: [{ id: 'item-1', name: { de_DE: 'Option 1' }, multi: '1' }] },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'selectedItem', value: ['item-1'] },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

});
