import { Interception } from 'cypress/types/net-stubbing';
import { Common } from '../../common';
import { IRequestData, UserFixture, UserTypes } from '../../Models';
import { Queries } from '../../queries';
import { RequestHandler } from '../../requestHandler';
import { Requests } from '../../requests';
import { IPage } from '../../interfaces/page-interface';
import Cookie = Cypress.Cookie;

export class Login implements IPage {

	public static visit(): void {
		Common.visitBackend('login');
	}

  /**
   * This is for quick check and should not be used for testing login page but rather for quickly
   * checking if we are on login page
   */
	public static isCorrectPage(): void {
		cy.get('.apto-backend-login-content').should('exist');
	}

  /**
   * This should be used only for testing login and not on other pages
   */
	public static isCorrectPageContent(): void {
    cy.get('form').should('exist');
    cy.get('form').find('md-card-title').should('exist').should('contain.text', 'Login');
    cy.get('#username').should('exist');
    cy.get('label[for=username]').should('exist').should('contain.text', 'Username:');
    cy.get('#password').should('exist');
    cy.get('label[for=password]').should('exist').should('contain.text', 'Password:');
    cy.get('#remember_me').should('exist');
    cy.get('md-card-actions').find('a').should('exist');
    cy.get('md-card-actions button[type=submit]').should('exist');
	}

  public static get initialQueryList(): IRequestData[] {
    return [
      Queries.FindCurrentUser,
      Queries.FindLanguages,
    ];
  }

  public static get initialCommandList(): IRequestData[] {
    return [];
  }

  public static get initialCustomRequestList(): IRequestData[] {
    return [
      Requests.messagesIsGrantedRequest,
    ];
  }

  public static get initialRequests(): IRequestData[] {
    return [
      ...Login.initialQueryList,
      ...Login.initialCommandList,
      ...Login.initialCustomRequestList,
    ];
  }

  private static get loginCookie(): string {
    return 'PHPSESSID';
  }

  /**
   * Makes login without checking if ok
   *
   * should be used everywhere where we need to just login and perform some actions afterward
   *
   * @param userType
   */
  public static login(userType: UserTypes = UserTypes.SUPERADMIN): Promise<any> {
    return new Cypress.Promise((resolve) => {
      Login.visit();
      Login.isCorrectPage();

      cy.fixture('user').then((user: UserFixture) => {
        cy.get('#username').type(user[userType].username);
        cy.get('#password').type(user[userType].password);

        RequestHandler.registerInterceptions(Login.initialRequests);

        cy.get('md-card-actions button[type=submit]').click();

        // all initial requests must be made without errors
        cy.wait(RequestHandler.getAliasesFromRequests(Login.initialRequests))
          .then(($response: Interception[]) => {
            resolve($response);
          });
      });
    });
  }

  /**
   * Checks if login was ok, should be called after login() method only once, we dont want to test on all pages if login
   * is ok. this must must be used only for testing login functionality
   *
   * @param response
   */
  public static loginAssert(response: Interception[]): void {
    response.forEach(($query) => {
      expect(RequestHandler.hasResponseError($query)).to.equal(false);
    });

    // ...And a session cookie must be generated
    cy.getCookie(Login.loginCookie).should('exist');

    // let's check that some elements must also exist (we do not check all of them)
    cy.get('md-sidenav').should('exist');
    cy.get('md-content').should('exist');
    cy.get('apto-user-status').should('exist');
  }

  /**
   * Makes logout without checking if ok
   */
  public static logout(): Promise<any> {
    return new Cypress.Promise((resolve) => {
      cy.getCookie(Login.loginCookie).then((cookieBeforeLogout: Cookie) => {
        cy.get('apto-user-status').should('exist');
        cy.get('[data-cy="logout-link"]').click();
        resolve(cookieBeforeLogout);
      });
    });
  }

  /**
   * Checks if logout ok
   *
   * should be used only once
   *
   * @param cookieBeforeLogout
   */
  public static logoutAssert(cookieBeforeLogout: Cookie): void {
    // Our PHPSESSID cookie must be different
    cy.getCookie(Login.loginCookie).then((cookieAfterLogout: Cookie) => {
      expect(cookieBeforeLogout.value).not.to.equal(cookieAfterLogout.value);

      // after clicking on logout link we should see "login" in our page link
      cy.url().should('include', 'login');

      // login form should exist
      Login.isCorrectPageContent();
    });
  }

  public static isUserLoggedIn(): void {
    // @todo implement this
  }
}
