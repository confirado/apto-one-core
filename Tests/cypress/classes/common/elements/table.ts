import { TableActionTypes } from '../../enums/table-action-types';

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
   * finds value in tables with type "md-table"
   *
   * provide column number for fast search
   *
   * @param text value to find in table
   */
  public static hasValue(text: string): typeof Table {
    cy.get(Table.initialSelector).should('exist').within(() => {
      cy.get('table tbody').within(() => {
        let found = false;

        cy.get('tr').each($tr => {
          // cy.wrap($tr).scrollIntoView();

          // @ts-ignore
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
      });
    });

    return Table;
  }

  /**
   * Table should not contain the given value
   *
   * @param text
   */
  public static hasNotValue(text: string): typeof Table {
    cy.get(Table.initialSelector).should('exist').within(() => {
      cy.get('table tbody').within(() => {
        let found = false;

        cy.get('tr').each($tr => {
          // @ts-ignore
          cy.wrap($tr).find('td').each($td => {
            const cellValue = $td.text().trim();

            if (cellValue.includes(text)) {
              found = true;
              return false; // means break the loop if found
            }
          });
        }).then(() => {
          expect(found).to.be.false;
        });
      });
    });

    return Table;
  }

  /**
   * Clicks one of the action button in table's right side
   *
   * searches the row with the given unique text and if finds clicks the action button int ath row
   *
   * @param type
   * @param uniqueValue value that we look in every table row and every cell
   */
  public static action(type: TableActionTypes, uniqueValue: any): typeof Table {
    cy.get(Table.initialSelector).should('exist').within(() => {
      cy.get('table tbody').within(() => {
        cy.get('tr').each($tr => {
          // @ts-ignore
          cy.wrap($tr).find('td').each($td => {
            const cellValue = $td.text().trim();

            if (cellValue.includes(uniqueValue)) {
              cy.wrap($tr).find('td:last-child').find(type).click({force: true});
              return false;
            }
          });
        })
       })
    });

    return Table;
  }

  /**
   * Selects the table cell by unique value then click on the given column.
   * makes sense for clicking on table's given row
   *
   * for example from the rules list page we want to click on the rule with the given name
   *
   * @param uniqueValue
   */
  public static selectRow(uniqueValue: any): typeof Table {
    cy.get(Table.initialSelector).should('exist').within(() => {
      cy.get('table tbody').within(() => {
        cy.get('tr').each($tr => {
          // @ts-ignore
          cy.wrap($tr).find('td').each($td => {
            const cellValue = $td.text().trim();

            if (cellValue.includes(uniqueValue)) {
              cy.wrap($tr).click({force: true});
              return false;
            }
          });
        })
      })
    });

    return Table;
  }

  /**
   * the same like selectRow, but you can also target the column you want
   * @param uniqueValue
   * @param columnNumber
   */
  public static selectCell(uniqueValue: any, columnNumber: number): typeof Table {
    cy.get(Table.initialSelector).should('exist').within(() => {
      cy.get('table tbody').within(() => {
        cy.get('tr').each($tr => {
          // @ts-ignore
          cy.wrap($tr).find('td').each($td => {
            const cellValue = $td.text().trim();

            if (cellValue.includes(uniqueValue)) {
              cy.wrap($tr).find('td:nth-child(' + columnNumber + ')').click({force: true});
              return false;
            }
          });
        })
      })
    });

    return Table;
  }
}
