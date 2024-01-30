export class Frontend {

  public static visit(url: string): void {
    cy.visit(Cypress.env('baseUrl') + url);
  }
}
