import { Login } from '../../classes/pages/login/login';
import { Product } from '../../classes/pages/product/product';
import { RequestHandler } from '../../classes/requestHandler';
import { Interception } from 'cypress/types/net-stubbing';
import { Backend } from '../../classes/common/backend';
import { Core } from '../../classes/common/core';
import { Material } from '../../classes/common/material';


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

              Material.checkbox('[data-cy="product-active"]', false, 'Aktiv');
              Material.checkbox('[data-cy="product-hidden"]', false, 'Versteckt');

              Material.input('[data-cy="product-identifier"]', '', 'Kennung:');
              Material.input('[data-cy="product-article-number"]', '', 'Artikelnummer:');
              Material.select('[data-cy="product-configuration-modes"]', 'OnePage', 'Konfiguratormodus');
              Material.select('[data-cy="product-keep-section-order"]', 'Ja', 'Reihenfolge der Sektionen einhalten');

              Material.input('[data-cy="product-position"]', 10, 'Position:');
              Material.input('[data-cy="product-stock"]', '', 'Lagerbestand:');
              Material.input('[data-cy="product-delivery-time"]', '', 'Lieferzeit(Tage):');
              Material.input('[data-cy="product-weight"]', '', 'Gewicht(kg):');
              Material.input('[data-cy="product-min-purchase"]', '', 'Mindestabnahme:');
              Material.input('[data-cy="product-max-purchase"]', '', 'Maximalabnahme:');
              Material.input('[data-cy="product-tax-rate"]', '', 'Steuersatz(%):');
              Material.select('[data-cy="product-price-calculator"]', '', 'Preisberechnung:', {'have.attr': 'required'});

              Material.input('[data-cy="product-name"]', '', 'Name (Deutsch):', {'have.attr': 'required'});
              Material.input('[data-cy="product-meta-title"]', '', 'Meta Titel (Deutsch):');

              Material.textarea('[data-cy="product-description"]', '', 'Beschreibung (Deutsch):');
              Material.textarea('[data-cy="product-meta-description"]', '', 'Meta Beschreibung (Deutsch):');

              Material.input('[data-cy="product-url"]', '', 'Produkt-Url:');

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
              Material.button('[data-cy="dialog-actions_button-cancel"]', 'Abbrechen', {'be.visible': null});
              Material.button('[data-cy="dialog-actions_button-new-save"]', 'Speichern', {'be.visible': null, 'have.class': 'md-primary'});
              Material.button('[data-cy="dialog-actions_button-save-and-insert"]', 'Speichern und hinzufügen', {'be.visible': null});

              // those 2 button are visible when we edit the product
              Material.button('[data-cy="dialog-actions_button-save-and-close"]', null, {'not.exist': null});
              Material.button('[data-cy="dialog-actions_button-edit-save"]', null, {'not.exist': null});

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
      .then(($elem) => {
        cy.dataCy('product-list').should('exist').within(() => {
          cy.get('md-table-container table tbody tr').should('exist').should('have.length', 1);
          cy.get('md-table-container table tbody tr td:nth-child(4)').should('contain.text', dummies.productName1);
        })
      })
  })

});
