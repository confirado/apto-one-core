import { Interception } from 'cypress/types/net-stubbing';
import { IProductListResponse, ProductList } from '../../classes/pages/product-list/product-list';
import { RequestHandler } from '../../classes/requestHandler';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Language } from '../../classes/language';
import { Core } from '../../classes/common/core';

describe('Product list', () => {

  const baseUrl = Cypress.env('baseUrl');

  beforeEach(() => {
    RequestHandler.registerInterceptions(ProductList.initialRequests);
    ProductList.visit();
  });

  it('Checks product list frontend', () => {

    ProductList.isCorrectPage();

    // if all requests are made
    cy.wait(RequestHandler.getAliasesFromRequests(ProductList.initialRequests)).then(($response: Interception[]) => {

      // check also that incoming product data is in sync with displayed products
      $response.forEach(($query) => {

        expect(RequestHandler.hasResponseError($query)).to.equal(false);

        if ($query.request.body.query === 'FindProductsByFilter') {

          const result = $query.response.body.result as IProductListResponse;

          // In browser, we should see in product list "numberOfRecords" amount of products
          cy.get('.product-wrapper').should('have.length', result.numberOfRecords);

          result.data.forEach((product: Product) => {
            const selector = `.product-wrapper[data-id="${product.id}"]`;

            // active products should be visible
            if (product.active) {
              cy.get(selector).should('exist');
            }

            // if product has image, it should not be broken
            if (product.previewImage && product.previewImage.length) {
              ProductList.hasProductPreviewImage(selector);
            }

            ProductList.hasTitle(selector);
            ProductList.hasDescription(selector);
            ProductList.isLinkOk(selector);
          });
        }
      });
    });
  });
});
