import { Login } from '../../classes/pages/login/login';
import { Product } from '../../classes/pages/product/product';
import { RequestHandler } from '../../classes/requestHandler';
import { Interception } from 'cypress/types/net-stubbing';
import { Backend } from '../../classes/common/backend';
import { Core } from '../../classes/common/core';
import { Select } from '../../classes/common/elements/form/select';
import { Checkbox } from '../../classes/common/elements/form/checkbox';
import { Input } from '../../classes/common/elements/form/input';
import { Textarea } from '../../classes/common/elements/form/textarea';
import { ProductList } from '../../classes/pages/product-list/product-list';
import { Table } from '../../classes/common/elements/table';
import { TranslatedValue } from '../../classes/common/elements/custom/translated-value';
import { MediaSelect } from '../../classes/common/elements/custom/media-select';
import { TableActionTypes } from '../../classes/enums/table-action-types';
import { Tabs } from '../../classes/common/elements/tabs';


// todo maybe each component must have it's within it's folder as classes and we can call them within our test
// but maybe do not use cypress's component testing rather use custom classes that have component test and can be loaded here


describe('Product', () => {

  var dummies = null;
  var productId = '';
  var productName1 = '';
  var productDescription1 = '';

  before(() => {
    productName1 = Product.generateName();
    productDescription1 = Product.generateDescription();
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


  it('Checks add product page "Product" tab if it contains the corrects elements when we first visit the page.', () => {

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests))
      .then(($responses: Interception[]) => {

        // todo check incoming data is also correct, check for that product list tests
        Core.checkResponsesForError($responses);
        Product.isCorrectPage();

        RequestHandler.registerInterceptions(Product.addProductQueryList);

        // click the add product button
        cy.dataCy('header_add-product-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
          .then(() => {

            // product name in title must be empty (as we did not create the product yet)
            cy.get('.product-title h3').find('span.title-headline').should('not.have.text');

            cy.get('.md-dialog-content').should('exist').then(() => {

              // check that from all tabs product tab ich clicked
              cy.dataCy('header_product-title').should('contain.text', 'Produkt');

              Tabs.get('md-tabs').hasContent();

              Checkbox.getByAttr('product-active')
                .hasLabel('Aktiv')
                .isUnChecked();

              Checkbox.getByAttr('product-hidden')
                .hasLabel('Versteckt')
                .isUnChecked();

              Input.getByAttr('product-identifier')
                .hasLabel('Kennung:')
                .hasValue('');

              Input.getByAttr('product-article-number')
                .hasLabel('Artikelnummer:')
                .hasValue('');

              Select.getByAttr('product-configuration-modes')
                .hasLabel('Konfiguratormodus')
                .hasValue('OnePage');

              Select.getByAttr('product-keep-section-order')
                .hasLabel('Reihenfolge der Sektionen einhalten')
                .hasValue('Ja');

              // position must be integer number and multiple of 10
              cy.dataCy('product-position').find('input').invoke('val').then(value => {
                expect(value).to.match(/^\d+$/);
                const intValue = parseInt(value, 10);
                expect(intValue).to.be.a('number').and.to.be.greaterThan(0);
                expect(intValue % 5).to.equal(0);
              });

              Input.getByAttr('product-stock')
                .hasLabel('Lagerbestand:')
                .hasValue('');

              Input.getByAttr('product-delivery-time')
                .hasLabel('Lieferzeit(Tage):')
                .hasValue('');

              Input.getByAttr('product-weight')
                .hasLabel('Gewicht(kg):')
                .hasValue('');

              Input.getByAttr('product-min-purchase')
                .hasLabel('Mindestabnahme:')
                .hasValue('');

              Input.getByAttr('product-max-purchase')
                .hasLabel('Maximalabnahme:')
                .hasValue('');

              Input.getByAttr('product-tax-rate')
                .hasLabel('Steuersatz(%):')
                .hasValue('');

              Select.getByAttr('product-price-calculator')
                .hasLabel('Preisberechnung:')
                .isNotSelected()
                .attributes({'have.attr': 'required'});

              Input.getByAttr('product-name')
                .hasLabel('Name (Deutsch):')
                .hasValue('')
                .attributes({'have.attr': 'required'});

              Input.getByAttr('product-meta-title')
                .hasLabel('Meta Titel (Deutsch):')
                .hasValue('');

              Textarea.getByAttr('product-description')
                .hasLabel('Beschreibung (Deutsch):')
                .hasValue('');

              Textarea.getByAttr('product-meta-description')
                .hasLabel('Meta Beschreibung (Deutsch):')
                .hasValue('');

              Input.getByAttr('product-url')
                .hasLabel('Produkt-Url:')
                .hasValue('');

              // Vorschaubild
              cy.dataCy('product-preview-picture-text').should('contain.text', 'Vorschaubild:');
              cy.dataCy('product-preview-picture').should('exist');
              cy.dataCy('media-select').find('label').should('have.text', 'Pfad:');
              cy.dataCy('media-select').find('input').should('have.value', '');
              cy.dataCy('media-select').find('md-icon').should('exist');

              cy.get('apto-media-select md-switch').find('.md-label').should('contain.text', 'Manuelle Eingabe');
            });
          });
        });
  });

  it('Checks add product page "Domains" tab if it contains the correct elements when we first visit the page.', () => {

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests))
      .then(($responses: Interception[]) => {

        // todo check incoming data is also correct, check for that product list tests
        Core.checkResponsesForError($responses);
        Product.isCorrectPage();

        RequestHandler.registerInterceptions(Product.addProductQueryList);

        // click the add product button
        cy.dataCy('header_add-product-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
          .then(() => {
            cy.get('.md-dialog-content').should('exist').then(() => {

              Tabs.get('md-tabs').hasContent();
              Tabs.get('md-tabs').select('Domains');

              cy.dataCy('domain-many-to-many').should('be.visible').then(() => {
                // Domaineigenschaften
                cy.dataCy('domain-title').should('have.text', 'Domaineigenschaften');

                // Domaineigenschaften data must not exist
                cy.dataCy('domain-properties').should('not.exist');

                // Verfügbar
                cy.dataCy('many-to-many-available-flat').should('be.visible');
                cy.dataCy('many-to-many-available-flat').find('.md-subheader .md-subheader-content').should('contain.text', 'Verfügbar');
                cy.dataCy('many-to-many-available-flat').find('md-list').invoke('text').then((text) => {
                  Core.isElementEmpty(text);
                });

                // Zugeordnet
                cy.dataCy('many-to-many-assigned').should('be.visible');
                cy.dataCy('many-to-many-assigned').find('.md-subheader .md-subheader-content').should('contain.text', 'Zugeordnet');
                cy.dataCy('many-to-many-assigned').find('md-list md-list-item').find('p').should('contain.text', 'Apto.ONE');
                cy.dataCy('many-to-many-assigned').find('md-list md-list-item').find('.md-secondary-container').find('button')
                  .should('have.class', 'md-warn').should('have.attr', 'title', 'Löschen');
              });
            });
          });
      });
  });

  it('Checks add product page "Kategorien" tab if it contains the correct elements when we first visit the page.', () => {

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests))
      .then(($responses: Interception[]) => {

        // todo check incoming data is also correct, check for that product list tests
        Core.checkResponsesForError($responses);
        Product.isCorrectPage();

        RequestHandler.registerInterceptions(Product.addProductQueryList);

        // click the add product button
        cy.dataCy('header_add-product-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
          .then(() => {
            cy.get('.md-dialog-content').should('exist').within(() => {

              Tabs.get('md-tabs').hasContent();
              Tabs.get('md-tabs').select('Kategorien');

              cy.dataCy('category-many-to-many').should('be.visible').within(() => {
                // Verfügbar
                cy.dataCy('many-to-many-available-tree').should('be.visible');
                cy.dataCy('many-to-many-available-tree').find('.md-subheader .md-subheader-content').should('contain.text', 'Verfügbar');

                // Zugeordnet
                cy.dataCy('many-to-many-assigned').should('be.visible');
                cy.dataCy('many-to-many-assigned').find('.md-subheader .md-subheader-content').should('contain.text', 'Zugeordnet');
                cy.dataCy('many-to-many-assigned').find('md-list').invoke('text').then((text) => {
                  Core.isElementEmpty(text);
                });
              });
            });
          });
      });
  });


  it('Checks product action buttons (bottom right)', () => {

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests))
      .then(($responses: Interception[]) => {

        // todo check incoming data is also correct, check for that product list tests
        Core.checkResponsesForError($responses);
        Product.isCorrectPage();

        RequestHandler.registerInterceptions(Product.addProductQueryList);

        // click the add product button
        cy.dataCy('header_add-product-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
          .then(() => {
            cy.get('md-dialog-actions').should('exist').within(() => {

              // check that buttons are there
              Product.cancelButton().should('contain.text', 'Abbrechen').should( 'be.visible');
              Product.saveNewButton().should('contain.text', 'Speichern').should( 'have.class', 'md-primary');
              Product.saveAndInsertButton().should('contain.text', 'Speichern und hinzufügen').should( 'be.visible');

              // those 2 button are visible when we edit the product
              Product.saveAndCloseButton().should( 'not.exist');
              Product.saveEditButton().should( 'not.exist');

              RequestHandler.registerInterceptions(Product.cancelProductsQueryList);

              // test cancel button
              Product.cancelButton().click();
            });
          });

        // "Abbrechen" button does it's job
        cy.wait(RequestHandler.getAliasesFromRequests(Product.cancelProductsQueryList))
          .then(($responses: Interception[]) => {
            Core.checkResponsesForError($responses);
            Product.isCorrectPage();
          });
      });
  });

  it('Checks create product is working', () => {

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests))
      .then(($responses: Interception[]) => {

        // todo check incoming data is also correct, check for that product list tests
        Core.checkResponsesForError($responses);
        Product.isCorrectPage();

        RequestHandler.registerInterceptions(Product.addProductQueryList);

        // click the add product button
        cy.dataCy('header_add-product-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Product.addProductQueryList))
          .then(() => {
            cy.get('md-dialog-actions').should('exist').then(() => {

              Product.saveNewButton().click();

              // name should not have value and we should see browser popup showing error
              cy.get('[data-cy="product-name"] input:invalid').should('have.length', 1);

              // after typing value error should dissapear
              cy.dataCy('product-name').type(productName1);
              cy.get('.product-title h3').find('span.title-headline').should('contain.text', productName1);

              Product.saveNewButton().click();
              cy.get('[data-cy="product-name"] input:invalid').should('have.length', 0);

              // lets try again saving
              Product.saveNewButton().click();

              Select.getByAttr('product-price-calculator')
                .hasError()
                .select(dummies.defaultPriceCalculator);

              RequestHandler.registerInterceptions(Product.saveProductRequests);

              // save button click
              Product.saveNewButton().click();
            });
          });
      });

    cy.wait(RequestHandler.getAliasesFromRequests(Product.saveProductRequests))
      .then(($responses: Interception[]) => {

        Core.checkResponsesForError($responses);

        // check that product is visible in product list page
        Table.getByAttr('product-list').hasValue(productName1);
      });
  });

  it('Checks if product exist in product list page right after making it', () => {

    // var productName1 = 'product-YgBii7tZ';

    // goto product list page and search for newly created product in previous step
    ProductList.visit();
    ProductList.searchNotFindProduct(productName1);

    RequestHandler.registerInterceptions(Product.editProductQueryList);
    Product.visit();

    // click on edit product button in product list page in backend
    Table.getByAttr('product-list').action(TableActionTypes.EDIT, productName1);

    // once edit product page is loaded
    cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {

      // read the product id
      cy.get('.product-title').find('span.title-id').invoke('text').then((id: string) => {

        productId = id.trim();

        // make product active
        Checkbox.getByAttr('product-active').check();

        TranslatedValue.getByAttr('product-description').writeValue(productDescription1);
        TranslatedValue.getByAttr('product-meta-title').writeValue(productName1);

        // take product url value from "kennung"
        Input.getByAttr('product-identifier').getValue().then((productIdentifier) => {
          Input.getByAttr('product-url').writeValue(productIdentifier);
        });

        MediaSelect.getByAttr('product-preview-picture').select('logo.png');

        // save product
        // Product.saveEditButton().click({ force: true });
        Product.saveAndCloseButton().click({ force: true });

        const selector = `.product-wrapper[data-id="${productId}"]`;

        // check that newly updated product has all updates we made
        ProductList.visit();
        ProductList.hasProduct(selector);
        ProductList.hasProductPreviewImage(selector);
        ProductList.hasProductTitle(selector);
        ProductList.hasProductDescription(selector);
        ProductList.isProductLinkOk(selector);
      });
    });
  });

  it('Checks delete product', () => {

    Product.createEmptyProduct(Product.generateName());

    // get the last product name in the list and delete it
    cy.dataCy('product-list').find('table tbody tr:last-child td:nth-child(4)').invoke('text').then((productName) => {
      Table.getByAttr('product-list').action(TableActionTypes.DELETE, productName);

      cy.get('md-dialog-actions').should('exist');
      cy.get('h2.md-title').should('contain.text', 'Den gewählten Eintrag wirklich löschen?');
      cy.get('.md-dialog-content-body p').should('contain.text', 'Das Löschen kann nicht rückgängig gemacht werden!');

      // check cancel button
      cy.get('md-dialog-actions').find('button.md-cancel-button')
        .should('exist').should('contain.text', 'Abbrechen')
        .click();
      cy.get('md-dialog-actions').should('not.exist');

      // check delete
      Table.getByAttr('product-list').action(TableActionTypes.DELETE, productName);

      RequestHandler.registerInterceptions(Product.removeProductRequests);

      cy.get('md-dialog-actions').find('button.md-confirm-button')
        .should('exist').should('contain.text', 'Löschen')
        .click();

      cy.get('md-dialog-actions').should('not.exist');

      // wait until delete requests are made
      cy.wait(RequestHandler.getAliasesFromRequests(Product.removeProductRequests)).then(($responses: Interception[]) => {

        Core.checkResponsesForError($responses);

        // the last item in table should not contain the product name
        cy.dataCy('product-list').within(() => {
          cy.get('table tbody').within(() => {
            cy.get('tr:last-child td:nth-child(4)').should('not.contain.text', productName);
          });
        });
      });
    });
  });

  it('Checks copy product', () => {

    Product.createEmptyProduct(Product.generateName());

    cy.dataCy('product-list').find('table tbody tr:last-child td:nth-child(4)').invoke('text').then((productName) => {

      RequestHandler.registerInterceptions(Product.copyProductRequests);

      Table.getByAttr('product-list')
        .action(TableActionTypes.COPY, productName);

      cy.wait(RequestHandler.getAliasesFromRequests(Product.copyProductRequests)).then(($responses: Interception[]) => {

        Core.checkResponsesForError($responses);

        // the last item in table should contain the product name
        cy.dataCy('product-list').within(() => {
          cy.get('table tbody').within(() => {
            cy.get('tr:last-child td:nth-child(4)').should('contain.text', productName);

            Product.deleteProductByName(productName);
          });
        });
      });
    });
  });


});
