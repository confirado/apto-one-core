export class Backend {

  public static visit(url = ''): void {
    if (url === 'login') {
      cy.visit(`${Cypress.env('baseUrl')}backend/${url}#!/`);
    }
    else {
      cy.visit(`${Cypress.env('baseUrl')}backend/#!/${url}`);
    }
  }

  /**
   * Example usage
   *
   * Common.clickBackendLeftMenuSubItem('Katalog', 'Produkte');
   *
   * @param parentSelector
   * @param subSelector
   */
  public static leftMenuItemClick(parentSelector: string, subSelector: string) {
    cy.dataCy(parentSelector).click();
    cy.get('.md-open-menu-container.md-active.md-clickable').should('be.visible');

    if (subSelector) {
      cy.dataCy(subSelector).click();
    }
  }

  /**
   * Clicks on the top tab item
   *
   * @param tabText text within the tab: Product | Domain ,...
   */
  public static topTabItemClick(tabText: string) {
    cy.get('md-tabs-wrapper').within(() => {
      cy.get('md-tabs-canvas').within(($mdCanvas) => {
        cy.wrap($mdCanvas).find('md-tab-item').each(($elm) => {

          // go to the given tab
          if ($elm.text() === tabText || $elm.text() === tabText.toUpperCase()) {
            cy.wrap($elm).click();
          }
        });
      });
    });
  }
}
