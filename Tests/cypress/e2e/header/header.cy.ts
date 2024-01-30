import { ShopResponse } from '@apto-base-frontend/store/shop/shop.model';
import { Interception } from 'cypress/types/net-stubbing';
import { ProductList } from '../../classes/pages/product-list/product-list';
import { ViewportPresetsEnum } from '../../classes/globals';
import { Queries } from '../../classes/queries';
import { RequestHandler } from '../../classes/requestHandler';
import { Core } from '../../classes/common/core';

describe('Header', () => {

  beforeEach(() => { });

  it('tests header on desktop (on product list page)', () => {

    RequestHandler.registerInterceptions(ProductList.initialRequests);

    ProductList.visit();
    ProductList.isCorrectPage();


    // logo
    cy.get('.logo').should('exist');

    cy.get('.logo').find('a img.visible-desktop')
      .should('exist')
      .should('be.visible')
      .should((img) => {
        Core.isImageLoadedCheck(img);
      });

    cy.get('.logo').find('a img.visible-mobile')
      .should('exist')
      .should('be.hidden');


    // Warenkorb test todo


    // languages todo sometimes is empty check why
    cy.wait(RequestHandler.getAliasesFromRequests(ProductList.initialRequests)).then(($response: Interception[]) => {

      let findShopContextResult = null;
      $response.forEach((response) => {
        expect(RequestHandler.hasResponseError(response)).to.equal(false);

        if (response.request.alias === Queries.FindShopContext.alias) {
          findShopContextResult = response.response.body.result as ShopResponse;
        }
      });


      // language select box
      // @todo this must be added when we add backend test as when we leave only one language language select does not exist any more
      cy.get('.language-mobile')
        .should('exist')
        .should('be.hidden');

      cy.get('.language-desktop')
        .should('exist')
        .should('be.visible');

      // check that the correct number of languages is shown
      cy.get('.language-desktop').find('mat-select').click();
      cy.get('.cdk-overlay-container').find('.mat-select-panel').find('.mat-option')
        .should('have.length', findShopContextResult.languages.length);
      cy.get('body').click();

      // click on language select and check that it contains languages from backend response
      cy.get('.language-desktop').find('mat-select').click();
      cy.get('.cdk-overlay-container').find('.mat-select-panel').should('be.visible');
      cy.get('.cdk-overlay-container').find('.mat-select-panel').find('.mat-option').each(($option, index) => {
        cy.wrap($option).invoke('text').then((language) => {
          cy.wrap($option).invoke('attr', 'data-locale').then((dataLocaleValue) => {

            // we must find each language from select box in incoming data
            const matchingLanguage = findShopContextResult.languages.find((resultLanguages) => resultLanguages.isocode === dataLocaleValue);
            expect(matchingLanguage).to.not.equal(undefined);
          });
        });
      });

      cy.get('body').click();
    });
  });

  it('tests header on mobile', () => {

    ProductList.visit();
    Core.switchViewport(ViewportPresetsEnum.MOBILE);

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
        Core.isImageLoadedCheck(img);
      });
  });
});
