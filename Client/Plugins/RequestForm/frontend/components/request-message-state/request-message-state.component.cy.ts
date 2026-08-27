import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { provideMockStore } from '@ngrx/store/testing';

import { AptoRequestFormFrontendModule } from '@apto-request-form-frontend/apto-request-form-frontend.module';
import { RequestMessageStateComponent } from './request-message-state.component';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

describe('RequestMessageStateComponent', () => {
  const messages = {
    success: { title: 'Erfolgreich', subtitle: 'Gesendet', message: 'Danke' },
    error: { title: 'Fehler', subtitle: 'Nicht gesendet', message: 'Bitte erneut versuchen' },
  };

  it('renders the sending state exclusively', () => {
    mount(RequestMessageStateComponent, {
      componentProperties: { state: { sending: true, success: false, error: false }, ...messages },
      imports: [AptoRequestFormFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    });

    cy.get('[data-cy="request-state-sending"]').should('be.visible');
    cy.get('[data-cy="request-state-success"], [data-cy="request-state-error"]').should('not.exist');
  });

  it('renders the success message for the terminal success state', () => {
    mount(RequestMessageStateComponent, {
      componentProperties: { state: { sending: false, success: true, error: false }, ...messages },
      imports: [AptoRequestFormFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    });
    cy.get('[data-cy="request-state-success"]').should('contain.text', 'Erfolgreich');
  });

  it('renders the error message for the terminal error state', () => {
    mount(RequestMessageStateComponent, {
      componentProperties: { state: { sending: false, success: false, error: true }, ...messages },
      imports: [AptoRequestFormFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [provideMockStore({ initialState: buildMockStoreInitialState() })],
    });
    cy.get('[data-cy="request-state-error"]').should('contain.text', 'Fehler');
  });
});
