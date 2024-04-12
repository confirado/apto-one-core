import { Table } from '../table';
import { TableActionTypes } from '../../../enums/table-action-types';
import { RequestHandler } from '../../../requestHandler';
import { Queries } from '../../../message-bus/queries';
import { ElementInterface } from '../../../interfaces/element-interface';
import Chainable = Cypress.Chainable;

export class MediaSelect implements ElementInterface {

  public static getByAttr(selector: string): typeof MediaSelect {
    cy.get(`[data-cy="${selector}"]`).as('MediaSelectElm');
    cy.get('@MediaSelectElm').should('exist');

    return MediaSelect;
  }

  public static get(selector: string): typeof MediaSelect {
    cy.get(selector).as('MediaSelectElm');
    cy.get('@MediaSelectElm').should('exist');

    return MediaSelect;
  }

  /**
   * Sets a custom cypress element for testing
   *
   * makes sense in cases when we don't select our element but rather we get it from search or so, then we can with this method make it as
   * testing object and apply all our methods to it
   *
   *  Checkbox.set(cy.dataCy('product-active'))
   *          .hasLabel('Aktiv')
   *          .isUnChecked();
   *
   * @param elem
   */
  public static set(elem: Chainable<JQuery<HTMLElement>>): typeof MediaSelect {
    elem.as('MediaSelectElm');

    return MediaSelect;
  }

  /**
   * Performs actions like download or delete on media popup elements
   *
   * // todo write logic for beeing able to select and perform actions from nested folders as well
   *
   * @param selector
   * @param action
   */
  public static action(selector: string, action: TableActionTypes): typeof MediaSelect {
    RequestHandler.registerInterceptions([Queries.ListMediaFiles]);

    // click on select media
    cy.get('@MediaSelectElm').find('input').click();

    cy.wait(RequestHandler.getAliasesFromRequests([Queries.ListMediaFiles])).then(() => {
      cy.get('.md-dialog-container').should('exist').then(() => {
        Table.get('apto-media-list').action(action, selector);
      });
    });

    return MediaSelect;
  }

  public static isImageSelected(imageName: string): typeof MediaSelect {
    cy.get('@MediaSelectElm')
      .find('apto-media-icon')
      .find('[data-cy="media-icon"]')
      .find('img')
      .should('exist')
      .and(($img) => {
        expect($img.attr('src')).to.include(imageName);
      });

      return MediaSelect;
  }

  public static select(selector: string, cell: number | null = 1 ): typeof MediaSelect {
    RequestHandler.registerInterceptions([Queries.ListMediaFiles]);

    // click on select media
    cy.get('@MediaSelectElm').find('input').click();

    cy.wait(RequestHandler.getAliasesFromRequests([Queries.ListMediaFiles])).then(() => {
      cy.get('.md-dialog-container').should('exist').then(() => {
        cy.get('.md-dialog-container').find('apto-media-list').should('exist');
          Table.get('apto-media-list').selectCell(selector, cell);
        });
      });

    return MediaSelect;
  }

}
