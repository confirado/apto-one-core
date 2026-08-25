import { TestBed } from '@angular/core/testing';
import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { Action } from '@ngrx/store';
import { MockStore, provideMockStore } from '@ngrx/store/testing';

import { ImageUploadComponent } from '@apto-image-upload-frontend/components/image-upload/image-upload.component';
import { AptoImageUploadFrontendModule } from '@apto-image-upload-frontend/apto-image-upload-frontend.module';
import { findEditableRenderImage, setCanvasElement } from '@apto-image-upload-frontend/store/canvas/canvas.actions';
import { setHideOnePage } from '@apto-catalog-frontend-configuration-actions';
import { Product } from '@apto-catalog-frontend-product-model';
import { buildProgressElement } from '@apto-catalog-frontend-progress-element-builder';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

interface DispatchSpy {
  getCalls(): Array<{ args: [Action] }>;
}

describe('ImageUploadComponent', () => {
  it('initializes the canvas and opens the designer for a new upload', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: {},
      },
      element: { id: 'element-42', sectionId: 'section-7' },
    });
    const product: Partial<Product> = { useStepByStep: true };

    mount(ImageUploadComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
      },
      imports: [
        AptoImageUploadFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    }).then(() => {
      const store = TestBed.inject(MockStore);
      cy.spy(store, 'dispatch').as('dispatchSpy');
    });

    cy.get('[data-cy="image-upload-show-designer"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actions = spy.getCalls().map((call) => call.args[0]);
      expect(actions).to.deep.equal([setCanvasElement({
        payload: {
          element: {
            elementId: 'element-42',
            sectionId: 'section-7',
            sectionRepetition: 0,
            staticValues: progressElement.element.definition.staticValues,
            state: {},
          },
        },
      }), setHideOnePage({ payload: true })]);
    });
  });

  it('also requests existing render images before opening the designer', () => {
    const progressElement = buildProgressElement({
      state: {
        id: 'element-42',
        sectionId: 'section-7',
        sectionRepetition: 0,
        values: {
          payload: {
            renderImages: [
              { perspective: 'front', renderImageId: 'render-1' },
              { perspective: 'front', renderImageId: 'render-2' },
            ],
          },
        },
      },
      element: { id: 'element-42', sectionId: 'section-7' },
    });
    const product: Partial<Product> = { useStepByStep: true };

    mount(ImageUploadComponent, {
      componentProperties: {
        element: progressElement,
        product: product as Product,
      },
      imports: [
        AptoImageUploadFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    }).then(() => {
      const store = TestBed.inject(MockStore);
      cy.spy(store, 'dispatch').as('dispatchSpy');
    });

    cy.get('[data-cy="image-upload-show-designer"]').click();

    cy.get<DispatchSpy>('@dispatchSpy').then((spy) => {
      const actions = spy.getCalls().map((call) => call.args[0]);
      expect(actions).to.deep.equal([setCanvasElement({
        payload: {
          element: {
            elementId: 'element-42',
            sectionId: 'section-7',
            sectionRepetition: 0,
            staticValues: progressElement.element.definition.staticValues,
            state: progressElement.state.values,
          },
        },
      }), findEditableRenderImage({
        payload: {
          perspective: 'front',
          renderImageIds: ['render-1', 'render-2'],
        },
      }), setHideOnePage({ payload: true })]);
    });
  });
});
