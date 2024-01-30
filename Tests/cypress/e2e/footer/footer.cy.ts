import { ProductList } from '../../classes/pages/product-list/product-list';
import { Core } from '../../classes/common/core';

describe('Footer', () => {
  const baseUrl = Cypress.env('baseUrl');

  beforeEach(() => { });

  it('test footer on desktop', () => {

    ProductList.visit();

    cy.get('apto-footer').should('exist');


    // links in footer (they set from content snippets and can be also not present)
    // @todo later test that display of links is correct
    // @todo later test that display of links can be clicked and bring to correct pages
    cy.get('.entries').should('exist');


    // copyright
    cy.get('.copyright').should('not.be.empty').should('contain.text', 'Copyright');


    // payment options image
    cy.get('.paymentOptions').should('be.visible');
    cy.get('.paymentOptions').find('img').should((img) => {
      Core.isImageLoadedCheck(img);
    });
  });


  it('test footer on mobile', () => {
    // ProductList.visit();
    // Core.switchViewport(ViewportPresetsEnum.MOBILE);
    // todo test links in footer that are not broken
  });
});
