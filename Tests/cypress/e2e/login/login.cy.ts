import { Login } from '../../classes/pages/login/login';

describe('Login functionality', () => {

  beforeEach(() => {});

  it('Checks login page if it has correct elements', () => {
    Login.visit();
    Login.isCorrectPage();
    Login.isCorrectPageContent();
  });

  it('Checks login with wrong credentials (empty or wrong data)', () => {
    Login.visit();
    Login.isCorrectPage();

    // try to submit the empty form
    cy.get('md-card-actions button[type=submit]').click();

    // this tests if browser's basic test for not empty field works (:invalid)
    cy.get('#username:invalid').should('have.length', 1);

    // test now with wrong data
    cy.get('#username').type('aaa');
    cy.get('#username:invalid').should('have.length', 0);

    // try to submit the form by providing only the username
    cy.get('md-card-actions button[type=submit]').click();

    cy.get('#password:invalid').should('have.length', 1);
    cy.get('#password').type('aaa');
    cy.get('#password:invalid').should('have.length', 0);

    // now try to submit the login form with wrong credentials (both fields)
    cy.get('md-card-actions button[type=submit]').click();

    // if the credentials are wrong we expect to stay on the same page
    cy.url().should('include', 'login');

    cy.get('.md-toolbar-tools').find('h2').should('contain', 'Fehlerhafte Zugangsdaten');
  });

  it('Checks login with correct credentials', () => {
    Login.login().then((data) => {
      Login.loginAssert(data);
    });
  });

  it('Logout and then login again', () => {
    Login.login().then(() => {
      Login.logout().then((data) => {
        Login.logoutAssert(data);
      });
    });
  });
});
