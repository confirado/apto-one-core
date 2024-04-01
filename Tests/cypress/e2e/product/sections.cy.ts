import { Login } from '../../classes/pages/login/login';
import { Product } from '../../classes/pages/product/product';
import { RequestHandler } from '../../classes/requestHandler';
import { Table } from '../../classes/common/elements/table';
import { TableActionTypes } from '../../classes/enums/table-action-types';
import { Checkbox } from '../../classes/common/elements/form/checkbox';
import { Sections } from '../../classes/pages/product/sections';
import { TranslatedValue, TranslatedValueTypes } from '../../classes/common/elements/custom/translated-value';
import { Tabs } from '../../classes/common/elements/tabs';
import { Interception } from 'cypress/types/net-stubbing';
import { Core } from '../../classes/common/core';

// todo maybe each component must have it's within it's folder as classes and we can call them within our test
// but maybe do not use cypress's component testing rather use custom classes that have component test and can be loaded here


describe('Sectionen', () => {

  var dummies = null;
  var productId = '';
  var productName1 = '';
  var sectionName1 = '';

  before(() => {
    productName1 = Product.generateName();
    sectionName1 = Sections.generateName();
  });

  beforeEach(() => {
    cy.fixture('dummies').then((data) => {
      dummies = data;
    });

    /*  Yes!, we have to do this in every test as cypress removes all the information about page in each test, after each test we get a
        blank page pointing nowhere
        Check this:
        https://docs.cypress.io/guides/core-concepts/test-isolation#Test-Isolation-Disabled   */
    Login.login()
      .then((data) => {
        RequestHandler.registerInterceptions(Product.initialRequests);
        Product.visitBackend(true);
    });
  });


  // todo maybe create product fist in before section


  it.only('Checks section tab after creating new product', () => {

    Product.createEmptyProduct(productName1);

    RequestHandler.registerInterceptions(Product.editProductQueryList);

    Table.getByAttr('product-list')
      .action(TableActionTypes.EDIT, productName1);

    cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {

      Tabs
        .get('md-tabs')
        .select('Sektionen');

      TranslatedValue
        .getByAttr('sections_name')
        .hasValue('', TranslatedValueTypes.INPUT);

      Checkbox
        .getByAttr('sections_add-default-element')
        .isUnChecked();

      cy.dataCy('sections_insert-button').should('have.attr', 'disabled');
    });
  });


  it.only('Checks section tab after creating new product', () => {

    // Product.createEmptyProduct(productName1);
    //
    // RequestHandler.registerInterceptions(Product.editProductQueryList);
    //
    // Table.getByAttr('product-list')
    //   .action(TableActionTypes.EDIT, productName1);
    //
    // cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {
    //
    //   Tabs
    //     .get('md-tabs')
    //     .select('Sektionen');
    //
    //   TranslatedValue
    //     .getByAttr('sections_name')
    //     .hasValue('', TranslatedValueTypes.INPUT);
    //
    //   Checkbox
    //     .getByAttr('sections_add-default-element')
    //     .isUnChecked();
    //
    //   cy.dataCy('sections_insert-button').should('have.attr', 'disabled');
    //

      // now let's create a section
      TranslatedValue
        .getByAttr('sections_name')
        .writeValue(sectionName1);

      RequestHandler.registerInterceptions(Sections.addSectionRequests);

      cy.dataCy('sections_insert-button').click();

      cy.wait(RequestHandler.getAliasesFromRequests(Sections.addSectionRequests)).then(($responses: Interception[]) => {
        Core.checkResponsesForError($responses);

        Table
          .getByAttr('sections_section-list')
          .rowIsUnChecked(sectionName1, 1)
          .rowIsUnChecked(sectionName1, 2)
          .hasValue(sectionName1);
      });
    });
    // });
});
