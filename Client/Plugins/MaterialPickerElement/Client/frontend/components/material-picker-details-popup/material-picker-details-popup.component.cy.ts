import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { provideMockStore } from '@ngrx/store/testing';

import { AptoMaterialPickerElementFrontendModule } from '@apto-material-picker-element-frontend/apto-material-picker-element-frontend.module';
import { MaterialPickerDetailsPopupComponent } from './material-picker-details-popup.component';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const poolItem: any = {
  material: {
    name: { de_DE: 'Eiche' }, description: { de_DE: 'Holz' }, reflection: null, transmission: null, absorption: null,
    properties: [], previewImage: { fileUrl: 'preview.png' }, galleryImages: [{ fileUrl: 'detail.png' }],
  },
  priceGroup: { additionalCharge: 0 },
};

describe('MaterialPickerDetailsPopupComponent', () => {
  it('selects a gallery image after opening with the preview image', () => {
    mount(MaterialPickerDetailsPopupComponent, {
      componentProperties: { data: { element: {} as any, poolItem } },
      imports: [AptoMaterialPickerElementFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    });

    cy.get('[data-cy="material-preview-image"]').should('have.class', 'gallery-image-selected');
    cy.get('[data-cy="material-gallery-image-detail.png"]').click().should('have.class', 'gallery-image-selected');
    cy.get('[data-cy="material-preview-image"]').should('not.have.class', 'gallery-image-selected');
  });
});
