import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { provideMockStore } from '@ngrx/store/testing';

import { AptoMaterialPickerElementFrontendModule } from '@apto-material-picker-element-frontend/apto-material-picker-element-frontend.module';
import { ItemLightPropertiesComponent } from './item-light-properties.component';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

describe('ItemLightPropertiesComponent', () => {
  it('renders only the supplied optical properties', () => {
    mount(ItemLightPropertiesComponent, {
      componentProperties: { reflection: 25, transmission: null, absorption: 75 },
      imports: [AptoMaterialPickerElementFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    });

    cy.get('[data-cy="material-light-reflection"]').should('contain.text', '25%');
    cy.get('[data-cy="material-light-absorption"]').should('contain.text', '75%');
    cy.get('[data-cy="material-light-transmission"]').should('not.exist');
  });
});
