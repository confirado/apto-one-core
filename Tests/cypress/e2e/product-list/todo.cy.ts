/// <reference types="cypress" />
context('Waiting', () => {
    beforeEach(() => {
        // cy.visit('/product/fenster-one-page')
        const baseUrl = Cypress.env('baseUrl');
        // Log the value of baseUrl to the Cypress command log
        cy.log(`baseUrl: ${baseUrl}`);
    })
    // BE CAREFUL of adding unnecessary wait times.
    // https://on.cypress.io/best-practices#Unnecessary-Waiting
    // https://on.cypress.io/wait
    it('cy.wait() - wait for a specific amount of time', () => {
        const baseUrl = Cypress.env('baseUrl');
        // Log the value of baseUrl to the Cypress command log
        cy.log(`baseUrl: ${baseUrl}`);
        cy.visit(baseUrl);
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
    })
})
