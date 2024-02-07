import { Login } from '../../classes/pages/login/login';
import { Product } from '../../classes/pages/product/product';
import { RequestHandler } from '../../classes/requestHandler';
import { Interception } from 'cypress/types/net-stubbing';
import { Backend } from '../../classes/common/backend';
import { Core } from '../../classes/common/core';
import { Select } from '../../classes/common/elements/select';
import { Checkbox } from '../../classes/common/elements/checkbox';
import { Input } from '../../classes/common/elements/input';
import { Textarea } from '../../classes/common/elements/textarea';


// todo maybe each component must have it's within it's folder as classes and we can call them within our test
// but maybe do not use cypress's component testing rather use custom classes that have component test and can be loaded here


describe('Product', () => {

  var dummies = null;

  beforeEach(() => {

    cy.fixture('dummies').then((data) => {
      dummies = data;
    })

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

              cy.get('md-tabs-content-wrapper').should('exist')

              Checkbox.getByAttr('product-active')
                .hasLabel('Aktiv')
                .unChecked();

              Checkbox.getByAttr('product-hidden')
                .hasLabel('Versteckt')
                .unChecked();

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

              cy.get('md-tabs-wrapper').should('exist');
              cy.get('md-tabs-content-wrapper').should('exist');

              Backend.topTabItemClick('Domains');

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

              cy.get('md-tabs-wrapper').should('exist');
              cy.get('md-tabs-content-wrapper').should('exist');

              Backend.topTabItemClick('Kategorien');

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


  it('Checks that action buttons underneath', () => {

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
              cy.dataCy('dialog-actions_button-cancel').should('contain.text', 'Abbrechen').should( 'be.visible');
              cy.dataCy('dialog-actions_button-new-save').should('contain.text', 'Speichern').should( 'have.class', 'md-primary');
              cy.dataCy('dialog-actions_button-save-and-insert').should('contain.text', 'Speichern und hinzufügen').should( 'be.visible');

              // those 2 button are visible when we edit the product
              cy.dataCy('dialog-actions_button-save-and-close').should( 'not.exist');
              cy.dataCy('dialog-actions_button-edit-save').should( 'not.exist');

              RequestHandler.registerInterceptions(Product.cancelProductsQueryList);

              // test cancel button
              cy.dataCy('dialog-actions_button-cancel').click();
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

              // save button click
              cy.dataCy('dialog-actions_button-new-save').click();

              // name should not have value and we should see browser popup shoing error
              cy.get('[data-cy="product-name"] input:invalid').should('have.length', 1);

              // after typing value error should dissapear
              cy.get('[data-cy="product-name"]').type(dummies.productName1);
              cy.get('.product-title h3').find('span.title-headline').should('contain.text', dummies.productName1);

              cy.dataCy('dialog-actions_button-new-save').click();
              cy.get('[data-cy="product-name"] input:invalid').should('have.length', 0);

              // lets try again saving
              cy.dataCy('dialog-actions_button-new-save').click();




              // todo refactor md-select, md-input logic so that they can handle also error checks
              // todo refactor md-select, to select value
              // this is required field as well
              cy.dataCy('product-price-calculator').should('have.class', 'md-input-invalid');

              cy.dataCy('product-price-calculator').click();
              cy.get('.md-select-menu-container.md-active.md-clickable').within(() => {
                cy.get('md-content md-option').find('.md-text').should('contain.text', dummies.defaultPriceCalculator).click();
              });

              // todo change the material.select to select values and check if selected
              cy.dataCy('product-price-calculator').find('.md-select-value').find('.md-text').should('contain.text', dummies.defaultPriceCalculator);




              RequestHandler.registerInterceptions(Product.saveProductRequests);

              // save button click
              cy.dataCy('dialog-actions_button-new-save').click();
            });
          });
      });

    cy.wait(RequestHandler.getAliasesFromRequests(Product.saveProductRequests))
      .then(($responses: Interception[]) => {

        Core.checkResponsesForError($responses);

        cy.dataCy('product-list').should('exist').within(() => {

          cy.get('md-table-container table tbody tr:last-child') // Select the last table row
            .within(() => {
              cy.get('td:nth-child(4)').should('contain.text', dummies.productName1);
            });
        });
      });
  });

  it('Checks product tabs right after creating it', () => {

  });

  it('Checks edit product', () => {

  });


});
