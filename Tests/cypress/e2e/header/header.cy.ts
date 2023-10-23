import { ShopResponse } from '@apto-base-frontend/store/shop/shop.model';
import { Interception } from 'cypress/types/net-stubbing';
import { ProductList } from '../../classes/product-list/product-list';
import { ViewportPresetsEnum } from '../../classes/globals';
import { Common } from '../../classes/common';
import { Queries } from '../../classes/queries';
import { RequestHandler } from '../../classes/requestHandler';

describe('Header', () => {

  beforeEach(() => { });

  it('tests header on desktop', () => {

    RequestHandler.interceptQuery(Queries.FindShopContext.alias);

    ProductList.visit();

    cy.get('apto-header').should('exist');


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


    // languages
    cy.wait(`@${Queries.FindShopContext.alias}`).then(($response: Interception) => {

      expect(RequestHandler.hasResponseError($response)).to.equal(false);

      const result = $response.response.body.result as ShopResponse;

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
        .should('have.length', result.languages.length);
      cy.get('body').click();

      // click on language select and check that it contains languages from backend response
      cy.get('.language-desktop').find('mat-select').click();
      cy.get('.cdk-overlay-container').find('.mat-select-panel').should('be.visible');
      cy.get('.cdk-overlay-container').find('.mat-select-panel').find('.mat-option').each(($option, index) => {
        cy.wrap($option).invoke('text').then((language) => {
          cy.wrap($option).invoke('attr', 'data-locale').then((dataLocaleValue) => {

            // we must find each language from select box in incoming data
            const matchingLanguage = result.languages.find((resultLanguages) => resultLanguages.isocode === dataLocaleValue);
            expect(matchingLanguage).to.not.equal(undefined);
          });
        });
      });

      cy.get('body').click();
    });
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
