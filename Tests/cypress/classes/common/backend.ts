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
}
