import { CyHttpMessages, Interception } from 'cypress/types/net-stubbing';
import { IRequestData, RequestTypes } from './Models';

export class RequestHandler {

  /**
   * Creates cypress intercept for requests
   *
   * @link https://docs.cypress.io/api/commands/intercept
   *
   * @param request
   */
  public static intercept(request: IRequestData): void {
    if (request.type === RequestTypes.QUERY || request.type === RequestTypes.COMMAND) {
      request.endpoint = `**/${request.endpoint}*`;
    }

    cy.intercept(request.method, request.endpoint).as(request.alias);
  }

  /**
   * Creates cypress intercept for "apto" queries
   *
   * @link https://docs.cypress.io/api/commands/intercept
   *
   * @param request
   */
  public static interceptQuery(request: IRequestData): void {
    cy.intercept(request.method, `**/${request.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, request.alias, RequestTypes.QUERY);
    });
  }

  /**
   * Creates cypress intercept for requests
   *
   * @link https://docs.cypress.io/api/commands/intercept
   *
   * @param request
   */
  public static interceptCommand(request: IRequestData): void {
    cy.intercept(request.method, `**/${request.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, request.alias, RequestTypes.COMMAND);
    });
  }

  /**
   * Request are not a common type like command or query and can be anything
   *
   * @param request
   */
  public static interceptRequest(request: IRequestData): void {
    RequestHandler.intercept(request);
  }

  /**
   * Registers interceptors for the given request list
   *
   * @link https://docs.cypress.io/api/commands/intercept
   *
   * @param requests
   */
  public static registerInterceptions(requests: IRequestData[]): void {
    requests.forEach((request) => {
      switch (request.type) {
        case RequestTypes.QUERY:
          RequestHandler.interceptQuery(request);
          break;
        case RequestTypes.COMMAND:
          RequestHandler.interceptCommand(request);
          break;
        case RequestTypes.REQUEST:
          RequestHandler.interceptRequest(request);
          break;
      }
    });
  }

  /**
   * Brings string to cypress alias name format
   *
   * @param alias
   */
  public static toAliasName(alias: string): string {
    return `@${alias}`;
  }

  /**
   * Creates alias array
   *
   * this is need for cypress "wait" method as argument in interceptions
   *
   * Example: ['@FindCurrentUser', '@FindLanguages', '@messagesIsGranted']
   *
   * @param requests
   */
  public static getAliasesFromRequests(requests: IRequestData[]): string[] {
    return requests.map((data) => RequestHandler.toAliasName(data.alias));
  }

  /**
   * Sets alias name for the query
   *
   * @link https://docs.cypress.io/guides/end-to-end-testing/working-with-graphql#Alias-multiple-queries-or-mutations
   *
   * @param req
   * @param operationName for example 'FindCurrentUser'
   * @param action
   * @private
   */
  private static setAlias(req: CyHttpMessages.IncomingRequest, operationName: string, action: RequestTypes): void {
    if (RequestHandler.hasOperationName(req, operationName, action)) {
      req.alias = operationName;
    }
  }

  /**
   * Before giving an alias name for the request, we use this method to onne more time check if we assign the alias to
   * the correct request. We wait until the request is done, then we add our alis to that request.
   * req argument here comes from cypress and includes all the data from request. If everything is correct, then our
   * alias must be in request's body. For example, for queries request's body looks like this:
   *
   * body:
   *   arguments: []
   *   query: "FindLanguages"
   *
   * therefore, we first check if the request type is correct, and then we check if it has the correct alias name
   *
   * example check: body.query = FindContentSnippetTree
   *
   * So why we do that? We could directly give an alias:
   *    cy.intercept(request.method, request.endpoint).as(request.alias);
   * The reason is that it allows us to some extra stuff between sending and receiving the request when we call a callback like that
   *
   * @param req
   * @param operationName example FindContentSnippetTree
   * @param action
   * @private
   */
  private static hasOperationName(req: CyHttpMessages.IncomingRequest, operationName: string, action: RequestTypes): boolean {
    const { body } = req;
    return (Object.prototype.hasOwnProperty.call(body, action) && body[action] === operationName);
  }

  public static hasResponseError(interception: Interception): boolean {
    const { response } = interception;
    return response.body.message.error;
  }

  // /**
  //  * Makes calls to the given endpoints
  //  *
  //  * @param requests
  //  */
  // public static fireFetch(requests: IRequestData[]): void {
  //   cy.window().then((window) => {
  //     requests.forEach((request) => {
  //       const url = `${Cypress.env('baseUrl')}${request.endpoint}`;
  //       window.fetch(url, {
  //         method: 'POST',
  //         body: JSON.stringify({ query: request.payload.query }),
  //       });
  //     });
  //   });
  // }
  //
  // public static waitAllRequests(requestAlias: string[]): Cypress.Chainable<Awaited<unknown>[]> {
  //   const elementDataArray = [];
  //
  //   return cy.wait(requestAlias).then(() => {
  //     cy.get(`${requestAlias}.all`).then(($requests) => {
  //       const promises = [];
  //       cy.wrap($requests).each((element: Interception) => {
  //         if (element.responseWaited === false) {
  //           promises.push(
  //             cy.wait(requestAlias).then(() => {
  //               elementDataArray.push(element);
  //             })
  //           );
  //         }
  //       });
  //
  //       return Promise.all(promises).then(() => elementDataArray);
  //     })}
  //   );
  // }
}
