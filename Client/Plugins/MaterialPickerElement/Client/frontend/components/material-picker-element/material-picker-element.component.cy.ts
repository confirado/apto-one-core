import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { MaterialPickerElementComponent } from '@apto-material-picker-element-frontend/components/material-picker-element/material-picker-element.component';
import { AptoMaterialPickerElementFrontendModule } from '@apto-material-picker-element-frontend/apto-material-picker-element-frontend.module';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { Product, Section } from '@apto-catalog-frontend-product-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const materialItem = {
  material: {
    id: 'material-1',
    name: { de_DE: 'Material 1' },
    previewImage: { path: 'material-1.png' },
  },
  priceGroup: { name: { de_DE: 'Standard' } },
};

const pickerState = {
  aptoMaterialPicker: {
    state: {
      items: [materialItem],
      colors: [],
      priceGroups: [],
      propertyGroups: [],
    },
  },
};

function pickerStaticValues(overrides: Record<string, any> = {}) {
  return {
    allowMultiple: false,
    secondaryMaterialActive: false,
    poolId: 'pool-1',
    sortByPosition: 'position',
    colorSectionActive: false,
    searchboxActive: false,
    priceGroupActive: false,
    showPriceGroupInMaterialName: false,
    ...overrides,
  } as any;
}

function pickerElement(overrides: { active?: boolean; values?: Record<string, any>; staticValues?: Record<string, any> } = {}) {
  return buildProgressElement({
    state: {
      id: 'element-42',
      sectionId: 'section-7',
      sectionRepetition: 0,
      active: overrides.active ?? false,
      values: overrides.values ?? {},
    },
    element: {
      id: 'element-42',
      sectionId: 'section-7',
      definition: {
        staticValues: pickerStaticValues(overrides.staticValues),
      },
    },
  });
}

function mountPicker(element: ReturnType<typeof pickerElement>, product: Partial<Product> = { id: 'product-1', useStepByStep: true }) {
  return mount(MaterialPickerElementComponent, {
    componentProperties: {
      element,
      product: product as Product,
      section: { allowMultiple: false } as Section,
    },
    imports: [
      AptoMaterialPickerElementFrontendModule,
      StoreModule.forRoot({}),
      EffectsModule.forRoot([]),
    ],
    providers: [
      provideMockStore({
        initialState: {
          ...buildMockStoreInitialState(),
          ...pickerState,
        },
      }),
    ],
  }).then(() => {
    const store = TestBed.inject(MockStore);
    cy.spy(store, 'dispatch').as('dispatchSpy');
  });
}

function expectedSingleSet(
  productId = 'product-1',
  secondary = { id: '', name: '', priceGroup: '' },
  materialColorMixing = 'monochrome'
) {
  return updateConfigurationState({
    updates: {
      set: [
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'aptoElementDefinitionId', value: 'apto-element-material-picker' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'poolId', value: 'pool-1' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'productId', value: productId },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialId', value: 'material-1' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialName', value: 'Material 1' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'priceGroup', value: 'Standard' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialIdSecondary', value: secondary.id },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialNameSecondary', value: secondary.name },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'priceGroupSecondary', value: secondary.priceGroup },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialsSecondary', value: [] },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorMixing', value: materialColorMixing },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorArrangement', value: 'alternately' },
        { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorQuantity', value: '' },
      ],
    },
  });
}

describe('MaterialPickerElementComponent', () => {
  it('dispatches the complete single-material configuration', () => {
    mountPicker(pickerElement());

    cy.get('[data-cy="material-item-material-1"]').click();

    cy.get('@dispatchSpy').then((spy: any) => {
      expect(spy.lastCall.args[0]).to.deep.equal(expectedSingleSet());
    });
  });

  it('dispatches the complete multi-material configuration', () => {
    mountPicker(pickerElement({ staticValues: { allowMultiple: true } }));

    cy.get('[data-cy="material-item-material-1"]').click();

    cy.get('@dispatchSpy').then((spy: any) => {
      const actualAction = spy.lastCall.args[0];
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'aptoElementDefinitionId', value: 'apto-element-material-picker' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'poolId', value: 'pool-1' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'productId', value: 'product-1' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialId', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialName', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'priceGroup', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materials', value: [{ id: 'material-1', name: 'Material 1', priceGroup: 'Standard' }] },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialIdSecondary', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialNameSecondary', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'priceGroupSecondary', value: '' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialsSecondary', value: [] },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorMixing', value: 'monochrome' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorArrangement', value: 'alternately' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'materialColorQuantity', value: '' },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('dispatches the remove action when the active single selection is cleared', () => {
    mountPicker(pickerElement({
      active: true,
      values: {
        materialId: 'material-1',
        materialName: 'Material 1',
        priceGroup: 'Standard',
      },
    }));

    cy.get('[data-cy="material-item-material-1"]').click();

    cy.get('@dispatchSpy').then((spy: any) => {
      expect(spy.lastCall.args[0]).to.deep.equal(updateConfigurationState({
        updates: {
          remove: [{
            sectionId: 'section-7',
            elementId: 'element-42',
            sectionRepetition: 0,
            property: null,
            value: null,
          }],
        },
      }));
    });
  });

  it('completes the secondary-material step before saving', () => {
    mountPicker(pickerElement({ staticValues: { secondaryMaterialActive: true } }));

    cy.get('[data-cy="material-item-material-1"]').click();
    cy.get('[data-cy="material-next-button"]').click();
    cy.get('[data-cy="material-multicolor-radio"]').click();
    cy.get('[data-cy="material-item-material-1"]').click();
    cy.get('[data-cy="material-save-button"]').click();

    cy.get('@dispatchSpy').then((spy: any) => {
      expect(spy.lastCall.args[0]).to.deep.equal(expectedSingleSet('product-1', {
        id: 'material-1',
        name: 'Material 1',
        priceGroup: 'Standard',
      }, 'multicolored'));
    });
  });
});
