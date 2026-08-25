import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { provideMockStore } from '@ngrx/store/testing';

import { AptoRequestFormFrontendModule } from '@apto-request-form-frontend/apto-request-form-frontend.module';
import { RequestFormComponent } from '@apto-request-form-frontend-request-form';
import { buildMockStoreInitialState, buildProduct } from '@apto-catalog-frontend-store-initial-state-builder';

interface OutputSpy<T> {
  getCalls(): Array<{ args: [T] }>;
}

interface RequestFormEmission {
  value: {
    email: string;
    declarationOfConsent: boolean;
  };
}

const requestFormSnippets = {
  name: 'plugins',
  content: {},
  children: [{
    name: 'requestForm',
    content: {},
    children: [{
      name: 'aptoSummary',
      content: {},
      children: [{
        name: 'values',
        content: {},
        children: [{
          name: 'gender',
          content: {},
          children: [
            { name: 'di', content: { de_DE: 'Divers' } },
            { name: 'mr', content: { de_DE: 'Herr' } },
            { name: 'ms', content: { de_DE: 'Frau' } },
          ],
        }],
      }, {
        name: 'fieldsWhenUserIsLoggedIn',
        content: { de_DE: 'ALL' },
      }],
    }],
  }],
};

describe('RequestFormComponent', () => {
  it('keeps submit disabled until required data and consent are valid', () => {
    mount(RequestFormComponent, {
      imports: [
        AptoRequestFormFrontendModule,
        StoreModule.forRoot({}),
        EffectsModule.forRoot([]),
      ],
      providers: [provideMockStore({
        initialState: buildMockStoreInitialState({
          aptoBase: {
            contentSnippets: { snippets: [requestFormSnippets] },
            language: { locale: 'de_DE' },
          },
          aptoCatalog: {
            product: { product: buildProduct({ minPurchase: 1, maxPurchase: 10 }) },
          },
        }),
      })],
    }).then(({ component }) => {
      cy.spy(component.requestFormChanged, 'emit').as('formChanged');
      cy.spy(component.sendRequestForm, 'emit').as('submit');
    });

    cy.get('[data-cy="request-form-submit"]').should('be.disabled');
    cy.get('[data-cy="request-form-gender"] [data-cy="select-field-trigger"]').click();
    cy.get('[data-cy="select-item-d"]').click();
    cy.get('[data-cy="request-form-email"] input').type('ada@example.test');
    cy.get('[data-cy="request-form-name"] input').type('Ada');
    cy.get('[data-cy="request-form-surname"] input').type('Lovelace');
    cy.get('[data-cy="request-form-submit"]').should('be.disabled');
    cy.get('[data-cy="request-form-consent"]').click();
    cy.get('[data-cy="request-form-submit"]').should('not.be.disabled').click();

    cy.get<OutputSpy<RequestFormEmission>>('@formChanged').should((formChanged) => {
      const emittedForms = formChanged.getCalls().map((call) => call.args[0]);
      expect(emittedForms.some((form) => form.value.email === 'ada@example.test' && form.value.declarationOfConsent === true)).to.equal(true);
    });
    cy.get<OutputSpy<undefined>>('@submit').should((submit) => expect(submit.getCalls()).to.have.length(1));
  });
});
