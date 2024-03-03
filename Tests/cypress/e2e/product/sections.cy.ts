import { Login } from '../../classes/pages/login/login';
import { Product } from '../../classes/pages/product/product';
import { RequestHandler } from '../../classes/requestHandler';
import { Table } from '../../classes/common/elements/table';
import { TableActionTypes } from '../../classes/enums/table-action-types';
import { Checkbox } from '../../classes/common/elements/form/checkbox';
import { Backend } from '../../classes/common/backend';
import { Sections } from '../../classes/pages/product/sections';
import { Translator } from '@angular/compiler-cli/linker/src/file_linker/translator';
import { TranslatedValue } from '../../classes/common/elements/custom/translated-value';

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
        Product.visit(true);
    });
  });


  it.only('Checks section tab after creating new product', () => {

    Product.createEmptyProduct(productName1);

    RequestHandler.registerInterceptions(Product.editProductQueryList);

    Table.getByAttr('product-list').action(TableActionTypes.EDIT, productName1);

    cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {

      Backend.topTabItemClick('Sektionen');

      TranslatedValue.getByAttr('sections_name').hasValue('');

      Checkbox.getByAttr('sections_add-default-element').isUnChecked();

      cy.dataCy('sections_insert-button').should('have.class', 'md-disabled');

      // todo check all inputs

      // todo then create section


    });

  });

});
