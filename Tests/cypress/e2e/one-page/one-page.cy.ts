describe('One page', () => {
  let baseUrl = Cypress.env('baseUrl');

  beforeEach(() => {
    cy.log(`baseUrl: ${baseUrl}`);
  });

  // todo this is not yet correct

  it('cy.wait() - wait for a specific amount of time', () => {
    cy.visit(baseUrl);
    cy.pause();

    cy.get('apto-input-field').eq(0).find('input').type('1200')
    cy.get('apto-input-field').eq(1).find('input').type('1200')
    cy.get('.apply-button').first().find('button').click()
    cy.get('.step-navigation-container').find('button[color=primary]').click()
    cy.get('.element-list').find('.element').first().click()
    cy.get('.step-navigation-container').find('button[color=primary]').click()
    cy.get('.element-list').find('.element').first().click()
    cy.get('.step-navigation-container').find('button[color=primary]').click()
    cy.get('#filtered-materials').find('.elements').first().click()
    cy.get('.apply-button').find('button[color=primary]').click()
    cy.get('.step-navigation-container').find('button[color=primary]').click()
    cy.get('apto-default-element-step-by-step').first().click()
    cy.get('.step-navigation-container').find('button[color=primary]').click()
  });
});
