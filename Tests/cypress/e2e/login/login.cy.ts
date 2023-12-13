import { Login } from '../../classes/login/login';

describe('Login functionality', () => {

  beforeEach(() => {});

  it('Checks login with wrong credentials', () => {
    Login.visit();
    Login.isCorrectPage();

    // first, we try to submit in the fields both empty and wrong data
    // try to submit the empty form
    cy.get('md-card-actions button[type=submit]').click();
    cy.get('#username:invalid').should('have.length', 1);

    cy.get('#username').type('aaa');

    cy.get('md-card-actions button[type=submit]').click();
    cy.get('#password:invalid').should('have.length', 1);

    cy.get('#password').type('aaa');

    // now try to submit the login form with wrong credentials
    cy.get('md-card-actions button[type=submit]').click();

    // if the credentials are wrong we expect to stay on the same page
    cy.url().should('include', 'login');

    cy.get('.md-toolbar-tools').find('h2').should('contain', 'Fehlerhafte Zugangsdaten');
  });

  it('Checks login with correct credentials', () => {
    Login.login();
  });

  it.only('Logout and then login again', () => {
    Login.login().then(() => {
      cy.get('apto-user-status').should('exist');
      Login.logout();
    });
  });
});
