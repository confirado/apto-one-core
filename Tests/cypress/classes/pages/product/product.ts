import { Queries } from '../../message-bus/queries';
import { IRequestData } from '../../models';
import { IPage } from '../../interfaces/page-interface';
import { SIDEBAR_LEFT_ITEMS } from '../../../_support/constants/constants';
import { Backend } from '../../common/backend';
import { Commands } from '../../message-bus/commands';
import { CryptoService } from '@apto-base-core/services/crypto-service';
import { RequestHandler } from '../../requestHandler';
import { Interception } from 'cypress/types/net-stubbing';
import { Core } from '../../common/core';
import { Table } from '../../common/elements/table';
import { Select } from '../../common/elements/form/select';

export class Product implements IPage {

  public static visit(visitByClick = false): void {

    if (visitByClick) {
      const parent = 'sidebar-left_' + SIDEBAR_LEFT_ITEMS?.katalog.labal;
      const sub = 'sidebar-left_sub_' + SIDEBAR_LEFT_ITEMS?.katalog.subItems.produkte.label;

      Backend.leftMenuItemClick(parent, sub);
    }
    else {
      Backend.visit('product');
    }
  }

  public static isCorrectPage(): void {
    cy.get('apto-page-header').should('exist');
    cy.get('apto-page-header .md-toolbar-tools').should('exist');
    cy.dataCy('header_product-title').should('contain.text', 'Produkte');
  }

  /**
   * do not use underscore as separator as in that case on saving product it is later changed to hyphen in product "Kennung" field
   *
   * @param characterCount
   */
  public static generateName(characterCount: number = 8) {
    return 'product-' + CryptoService.generateRandomString(characterCount);
  }

  public static saveNewButton(): Cypress.Chainable<JQuery<HTMLElement>> {
    return cy.dataCy('dialog-actions_button-new-save');
  }

  public static saveEditButton(): Cypress.Chainable<JQuery<HTMLElement>> {
    return cy.dataCy('dialog-actions_button-edit-save');
  }

  public static cancelButton(): Cypress.Chainable<JQuery<HTMLElement>> {
    return cy.dataCy('dialog-actions_button-cancel');
  }

  public static saveAndInsertButton(): Cypress.Chainable<JQuery<HTMLElement>> {
    return cy.dataCy('dialog-actions_button-save-and-insert');
  }

  public static saveAndCloseButton(): Cypress.Chainable<JQuery<HTMLElement>> {
    return cy.dataCy('dialog-actions_button-save-and-close');
  }

  /**
   * do not use underscore as separator as in that case on saving product it is later changed to hyphen in product "Kennung" field
   *
   * @param characterCount
   */
  public static generateDescription(characterCount: number = 8) {
    return 'product-desc-' + CryptoService.generateRandomString(characterCount);
  }

  /**
   * Creates empty product without making a lot o tests
   *
   * @param productName
   */
  public static createEmptyProduct(productName?: string|null) {

    const name = productName ?? Product.generateName();

    cy.fixture('dummies').then((data) => {
      let dummies = data;

      RequestHandler.registerInterceptions(Product.addProductQueryList);

      // click the add product button
      cy.dataCy('header_add-product-button').click();

      cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
        .then(() => {
          cy.get('md-dialog-actions').should('exist').then(() => {

            // after typing value error should dissapear
            cy.get('[data-cy="product-name"]').type(name);
            cy.get('.product-title h3').find('span.title-headline').should('contain.text', name);

            Select.getByAttr('product-price-calculator').select(dummies.defaultPriceCalculator);
            RequestHandler.registerInterceptions(Product.saveProductRequests);
            Product.saveNewButton().click();
          });
        });
    });

    cy.wait(RequestHandler.getAliasesFromRequests(Product.saveProductRequests))
      .then(($responses: Interception[]) => {
        Core.checkResponsesForError($responses);
        Table.getByAttr('product-list').hasValue(name);
      });
  }

  public static isCorrectPageContent(): void { }

  public static get initialQueryList(): IRequestData[] {
    return [
      Queries.FindProductsByFilterPagination,
      Queries.FindCategories,
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
      ...Product.initialQueryList,
      ...Product.initialCommandList,
      ...Product.initialCustomRequestList,
    ];
  }

  public static get addProductQueryList(): IRequestData[] {
    return [
      Queries.FindFilterProperties,
      Queries.FindCategoryTree,
      Queries.FindCustomerGroups,
      Queries.FindPriceCalculators,
      Queries.FindShops,
      Queries.FindNextAvailablePosition,
    ];
  }

  public static get editProductQueryList(): IRequestData[] {
    return [
      Queries.FindFilterProperties,
      Queries.FindCategoryTree,
      Queries.FindCustomerGroups,
      Queries.FindPriceCalculators,
      Queries.FindShops,
      Queries.FindProduct,
      Queries.FindProductSections,
      Queries.FindProductRules,
      Queries.FindProductComputedValues,
      Queries.FindProductPrices,
      Queries.FindProductDiscounts,
      Queries.FindProductCustomProperties,
      Queries.FindUsedCustomPropertyKeys,
    ];
  }

  /**
   * fired when clicking on "Abbrechen" button in right bottom corner
   */
  public static get cancelProductsQueryList(): IRequestData[] {
    return [
      Queries.FindProducts,
      Queries.FindCategories,
    ];
  }

  /**
   * we click on save product button (Speichern)
   */
  public static get saveProductRequests(): IRequestData[] {
    return [
      Queries.FindProducts,
      Queries.FindCategories,
      Commands.AddProduct,
    ];
  }

  /**
   * We click on delete product button
   */
  public static get removeProductRequests(): IRequestData[] {
    return [
      Commands.RemoveProduct,
      Queries.FindProductsByFilterPagination,
    ];
  }
}
