import { ProductList } from '../../classes/product-list/product-list';
import { ExistingLanguages, ViewportPresets, ViewportPresetsEnum } from '../../classes/globals';
import { Common } from '../../classes/common';

describe('Header', () => {
  const baseUrl = Cypress.env('baseUrl');

  beforeEach(() => { });

  it('tests header on desktop', () => {

    ProductList.visit();

    cy.get('apto-header').should('exist');


    // language select box
    // @todo this must be added when we add backend test as when we leave only one language language select does not exist any more
    // @ todo test mobile case as well
    cy.get('.language-mobile')
      .should('exist')
      .should('be.hidden');

    cy.get('.language-desktop')
      .should('exist')
      .should('be.visible');

    // click on it and check that it contains correct number of languages available
    cy.get('.language-desktop').find('mat-select').click();
    cy.get('.cdk-overlay-container').find('.mat-select-panel').should('be.visible');
    cy.get('.cdk-overlay-container').find('.mat-select-panel').find('.mat-option').each(($option) => {
      cy.wrap($option).invoke('text').then((text) => {
        // languages in the select box must be one of the existing ones
        expect(Object.values(ExistingLanguages)).to.include(text.trim());
      });
    });

    cy.get('body').click();


    // logo
    cy.get('.logo').should('exist');

    cy.get('.logo').find('a img.visible-desktop')
      .should('exist')
      .should('be.visible')
      .should((img) => {
        Common.isImageLoadedCheck(img);
      });

    cy.get('.logo').find('a img.visible-mobile')
      .should('exist')
      .should('be.hidden');
  });

  it('tests header on mobile', () => {

    ProductList.visit();
    Common.switchViewport(ViewportPresetsEnum.MOBILE);

    // language select box
    cy.get('.language-desktop')
      .should('exist')
      .should('be.hidden');

    cy.get('.language-mobile')
      .should('exist')
      .should('be.visible');

    // logo
    cy.get('.logo').find('a img.visible-desktop')
      .should('exist')
      .should('be.hidden');

    cy.get('.logo').find('a img.visible-mobile')
      .should('exist')
      .should('be.visible')
      .should((img) => {
        Common.isImageLoadedCheck(img);
      });
  });
});
