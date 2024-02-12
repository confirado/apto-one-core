import { number } from 'mathjs';

export class Table {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Table {
    Table.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Table.initialSelector).should('exist');

    return Table;
  }

  public static get(selector: string): typeof Table {
    Table.initialSelector = selector;
    cy.get(Table.initialSelector).should('exist');

    return Table;
  }

  /**
   * finds value in table
   *
   * provide column number for fast search
   *
   * @param text value to find in table
   * @param column provide column number ofr fast search
   */
  public static hasValue(text: string, column: number | null = null): typeof Table {
    cy.get('md-table-container table tbody').within(() => {
      let found = false;

      if (column === null) {
        cy.get('tr').each($tr => {
          cy.wrap($tr).find('td').each($td => {
            const cellValue = $td.text().trim();

            if (cellValue.includes(text)) {
              found = true;
              return false; // means break the loop if found
            }
          });
        }).then(() => {
          expect(found).to.be.true;
        });
      } else {
        cy.get('td:nth-child(' + column + ')').should('contain.text', text);
      }
    });

    return Table;
  }
}
