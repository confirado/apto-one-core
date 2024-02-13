import { Table } from '../table';
import { TableActionTypes } from '../../../enums/table-action-types';
import { RequestHandler } from '../../../requestHandler';
import { Queries } from '../../../message-bus/queries';

export class MediaSelect {

  private static initialSelector: string;

  public static getByAttr(selector: string): typeof MediaSelect {
    MediaSelect.initialSelector = `[data-cy="${selector}"]`;
    cy.get(MediaSelect.initialSelector).should('exist');

    return MediaSelect;
  }

  public static get(selector: string): typeof MediaSelect {
    MediaSelect.initialSelector = selector;
    cy.get(MediaSelect.initialSelector).should('exist');

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
    cy.get(MediaSelect.initialSelector).find('input').click();

    cy.wait(RequestHandler.getAliasesFromRequests([Queries.ListMediaFiles])).then(() => {
      cy.get('.md-dialog-container').should('exist').then(() => {
        Table.get('apto-media-list').action(action, selector);
      });
    });

    return MediaSelect;
  }


  public static select(selector: string, cell: number | null = 1 ): typeof MediaSelect {
    RequestHandler.registerInterceptions([Queries.ListMediaFiles]);

    // click on select media
    cy.get(MediaSelect.initialSelector).find('input').click();

    cy.wait(RequestHandler.getAliasesFromRequests([Queries.ListMediaFiles])).then(() => {
      cy.get('.md-dialog-container').should('exist').then(() => {
        Table.get('apto-media-list').selectCell(selector, cell);
      });
    });

    return MediaSelect;
  }

}
