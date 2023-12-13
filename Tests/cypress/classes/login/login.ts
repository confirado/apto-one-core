import { Common } from '../common';
import { IRequestData, UserFixture, UserTypes } from '../Models';
import { Queries } from '../queries';
import { RequestHandler } from '../requestHandler';
import { Requests } from '../requests';
import { IPage } from '../interfaces/page-interface';

export class Login implements IPage {

	public static visit(): void {
		Common.visitBackend('login');
	}

	public static isCorrectPage(): void {
		cy.get('.apto-backend-login-content').should('exist');
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

  public static login(userType: UserTypes = UserTypes.SUPERADMIN): Promise<any> {
    return new Cypress.Promise((resolve) => {
      Login.visit();
      Login.isCorrectPage();

      cy.fixture('user').then((user: UserFixture) => {
        cy.get('#username').type(user[userType].username);
        cy.get('#password').type(user[userType].password);

        // Login.initialQueryList.forEach((request) => RequestHandler.interceptQuery(request.alias));
        // Login.initialCommandList.forEach((request) => RequestHandler.interceptCommand(request.alias));
        // Login.initialCustomRequestList.forEach((request) => RequestHandler.interceptRequest(request));

        RequestHandler.registerInterceptions([
          ...Login.initialQueryList,
          ...Login.initialCommandList,
          ...Login.initialCustomRequestList,
        ]);

        // cy.get('md-card-actions button[type=submit]').click();
        //
        // cy.wait(RequestHandler.getWaitList())
        // cy.wait([...RequestHandler.getQueryAliasLsit(Login.initialQueryList), RequestHandler.toAliasName(Requests.messagesIsGrantedRequest.alias)])
        //   .then(($response: Interception[]) => {
        //     $response.forEach(($query) => {
        //       expect(RequestHandler.hasResponseError($query))
        //         .to
        //         .equal(false);
        //     });
        //     resolve();
        //   });
      });
    });
  }

  public static logout(): void {
    cy.get('[data-cy="logout-link"]').click();
    cy.url().should('include', 'login');
    cy.get('.apto-backend-login-content').should('exist');
  }

  public static isUserLoggedIn(): void {
    // @todo implement this
  }
}
