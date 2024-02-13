import { Page } from '@apto-catalog-frontend/models/pagination';
import { Queries } from '../../message-bus/queries';
import { IRequestData } from '../../models';
import { IPage } from '../../interfaces/page-interface';
import { RequestHandler } from '../../requestHandler';
import { Core } from '../../common/core';

export interface IProductListResponse extends Page<any>{}

export class ProductList implements IPage {

  public static visit(): void {
    cy.visit(Cypress.env('baseUrl'));
  }

  public static isCorrectPage(): void {
    cy.get('apto-product-list').should('exist');
  }

  public static isCorrectPageContent(): void { }

  public static get initialQueryList(): IRequestData[] {
    return [
      Queries.FindShopContext,
      Queries.FindContentSnippetTree,
      Queries.FindProductsByFilter,
    ];
  }

  public static get initialCommandList(): IRequestData[] {
    return [];
  }

  public static get initialCustomRequestList(): IRequestData[] {
    return [];
  }

  public static get initialRequests(): IRequestData[] {
    return [
      ...ProductList.initialQueryList,
      ...ProductList.initialCommandList,
      ...ProductList.initialCustomRequestList,
    ];
  }

  /**
   * Searches for the given product
   *
   * asserts it must be found
   *
   * @param productName
   */
  public static hasProduct(productName: string): Cypress.Chainable<any> {

    cy.get('apto-product-list').should('exist');

    RequestHandler.registerInterceptions([Queries.FindProductsByFilter]);

    cy.get('apto-search').find('.search-field input').clear().type(productName);

    return cy.wait(RequestHandler.getAliasesFromRequests([Queries.FindProductsByFilter])).then(() => {
      cy.get('.product-wrapper').should('have.length', 1);
    });
  }

  /**
   * Searches for the given product
   *
   * asserts it must not be found
   *
   * @param productName
   */
  public static hasNotProduct(productName: string): Cypress.Chainable<any> {
    cy.get('apto-product-list').should('exist');

    RequestHandler.registerInterceptions([Queries.FindProductsByFilter]);

    cy.get('apto-search').find('.search-field input').clear().type(productName);

    return cy.wait(RequestHandler.getAliasesFromRequests([Queries.FindProductsByFilter])).then(() => {
      cy.get('.product-wrapper').should('have.length', 0);
    });
  }

  public static hasProductPreviewImage(selector: string) {
    cy.get(selector).find('img').should((img) => {
      Core.isImageLoadedCheck(img);
    });
  }

  public static hasTitle(selector: string, title: string|null = null) {
    if (title) {
      cy.get(selector).find('.product-description').find('h3').should('contain.text', title);
    } else {
      cy.get(selector).find('.product-description').find('h3').should('not.be.empty');
    }
  }

  public static hasDescription(selector: string, description: string|null = null) {
    if (description) {
      cy.get(selector).find('.product-description').find('.description').should('contain.text', description);
    } else {
      cy.get(selector).find('.product-description').find('.description').should('not.be.empty');
    }
  }

  /**
   * check that link for the product is clickable and exist
   *
   * @param selector
   */
  public static isLinkOk(selector: string) {
    const baseUrl = Cypress.env('baseUrl');

    cy.get(selector)
      .find('.product-description button')
      .invoke('attr', 'data-link')
      .then((link) => {
        cy.wrap(link).should('exist');
        cy.wrap(link).should('not.be.empty');

        Core.isLinkBrokenTest(`${baseUrl}#${link}`);
      });
  }
}
