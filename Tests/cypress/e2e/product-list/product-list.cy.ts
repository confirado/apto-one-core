import { Interception } from 'cypress/types/net-stubbing';
import { IProductListResponse, ProductList } from '../../classes/product-list/product-list';
import { RequestHandler } from '../../classes/requestHandler';
import { Common } from '../../classes/common';

describe('Product list', () => {

  const baseUrl = Cypress.env('baseUrl');

  beforeEach(() => {
    const requests = ProductList.initialRequestList;

    requests.forEach((request) => RequestHandler.interceptQuery(request.alias));

    ProductList.visit();
  });

  it('Checks that all requests are made', () => {

    // if all requests are made
    cy.wait(ProductList.initialAliasList).then(($queries: Interception[]) => {

      // check also that incoming product data is in sync with displayed products
      $queries.forEach(($query) => {

        if ($query.request.body.query === 'FindProductsByFilter') {

          const result = $query.response.body.result as IProductListResponse;

          // we should see in product list "numberOfRecords" amount of products
          cy.get('.product-wrapper').should('have.length', result.numberOfRecords);

          result.data.forEach((product) => {
            // active products should be visible
            if (product.active) {
              cy.get(`.product-wrapper[data-id="${product.id}"]`).should('exist');
            }

            // if product has image it should not be broken
            if (product.previewImage && product.previewImage.length) {
              cy.get(`.product-wrapper[data-id="${product.id}"]`).find('img').should((img) => {
                Common.isImageLoadedCheck(img);
              });
            }

            // check that links for each product aare clickable and exist
            cy.get(`.product-wrapper[data-id="${product.id}"]`)
              .find('.product-description button')
              .invoke('attr', 'data-link')
              .then((link) => {
                cy.wrap(link).should('exist');
                cy.wrap(link).should('not.be.empty');

                cy.request(`${baseUrl}#${link}`)
                  .then((response) => {
                    expect(response.status).to.be.within(200, 299);
                  });
              });
          });
        }
      });
    });
  });
});
