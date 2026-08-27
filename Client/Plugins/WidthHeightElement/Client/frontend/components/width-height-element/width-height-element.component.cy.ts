import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { WidthHeightElementComponent } from '@element-definition-width-height-element';
import { AptoWidthHeightElementFrontendModule } from '@apto-width-height-element-frontend/apto-width-height-element-frontend.module';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { updateConfigurationState } from '@apto-catalog-frontend-configuration-actions';
import { Product, Section } from '@apto-catalog-frontend-product-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const range = [{ type: 'range', minimum: 1, maximum: 30, step: 1 }];

interface DispatchSpy {
  getCalls(): Array<{ args: [{ type: string }] }>;
}

describe('WidthHeightElementComponent', () => {
  it('dispatches both entered dimensions', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: { height: 10, width: 20 },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: { height: range, width: range },
          staticValues: {
            renderingHeight: 'input',
            renderingWidth: 'input',
            defaultHeight: '10',
            defaultWidth: '20',
          },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(WidthHeightElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoWidthHeightElementFrontendModule,
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
    cy.get('[data-cy="width-height-height-input"] input').clear().type('12').should('have.value', '12');
    cy.get('[data-cy="width-height-width-input"] input').clear().type('24');
    cy.get('[data-cy="width-height-width-input"] input').should('have.value', '24');
    cy.tick(100);
    cy.get('[data-cy="width-height-apply-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'height', value: 12 },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'width', value: 24 },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('dispatches selected dimensions when both fields use select rendering', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: { height: '10', width: '20' },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: { height: range, width: range },
          staticValues: {
            renderingHeight: 'select',
            renderingWidth: 'select',
            defaultHeight: '10',
            defaultWidth: '20',
          },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(WidthHeightElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoWidthHeightElementFrontendModule,
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

    cy.get('[data-cy="width-height-height-select"] [data-cy="select-field-trigger"]').click();
    cy.get('[data-cy="select-item-12"]').click();
    cy.get('[data-cy="width-height-width-select"] [data-cy="select-field-trigger"]').click();
    cy.get('[data-cy="select-item-24"]').click();
    cy.get('[data-cy="width-height-apply-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          set: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'height', value: '12' },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 0, property: 'width', value: '24' },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });

  it('dispatches remove updates for the active dimensions', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 1,
        active: true,
        values: { height: 10, width: 20 },
      },
      element: {
        id: 'element-42',
        sectionId: 'section-7',
        definition: {
          properties: { height: range, width: range },
          staticValues: { renderingHeight: 'input', renderingWidth: 'input' },
        },
      },
    });

    const product: Partial<Product> = { useStepByStep: true };
    const section: Partial<Section> = { allowMultiple: false };

    mount(WidthHeightElementComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
        section: section as Section,
      },
      imports: [
        AptoWidthHeightElementFrontendModule,
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

    cy.get('[data-cy="width-height-remove-button"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actualAction = spy.getCalls().map((call) => call.args[0]).find((action) => action.type === updateConfigurationState.type);
      const expectedAction = updateConfigurationState({
        updates: {
          remove: [
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 1, property: 'height', value: 10 },
            { sectionId: 'section-7', elementId: 'element-42', sectionRepetition: 1, property: 'width', value: 20 },
          ],
        },
      });

      expect(actualAction).to.deep.equal(expectedAction);
    });
  });
});
