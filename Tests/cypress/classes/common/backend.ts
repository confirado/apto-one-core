export class Backend {

  public static visit(url: string): void {
    cy.visit(`${Cypress.env('baseUrl')}backend/#!/${url}`);
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
    cy.get('md-tabs-wrapper').should('exist').within(() => {
      cy.get('md-tabs-canvas').should('exist').within(($mdCanvas) => {
        cy.wrap($mdCanvas).find('md-tab-item').each(($elm) => {

          // go to the given tab
          if ($elm.text() === 'tabText') {
            cy.wrap($elm).click();
          }
        });
      });
    });
  }

}
