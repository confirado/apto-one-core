import { CyHttpMessages, Interception } from 'cypress/types/net-stubbing';
import { IRequestData, RequestTypes } from './models';

export class RequestHandler {

  /**
   * Creates cypress intercept for requests
   *
   * @link https://docs.cypress.io/api/commands/intercept
   *
   * @param request
   */
  public static intercept(request: IRequestData): void {

    /* In case of commands and queries, we don't pass the exact endpoint but rather the relative one. Because otherwise
       we have a problem in cypress as it cannot distinguish between multiple requests.
       In the case of custom requests (not command nor query) we take the endpoint as it is  */
    if (request.type === RequestTypes.QUERY || request.type === RequestTypes.COMMAND) {
      request.endpoint = `**/${request.endpoint}*`;
    }

    /* We make first the call/request and only then we add interception alias, this is because of cypress, as it is not
       working in the simple way in case of multiple interceptions, it fails to distinguish the requests from
       one another, and as a result in our "wait" we get for the 3 different calls just 3 identical calls.
       But registering interceptions with callback function provides us also the ability to do some extra stuff before
       and after requests are made  */
    cy.intercept(request.method, request.endpoint, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, request.alias, request.type);
    });
  }

  /**
   * Sets alias name for the request
   *
   * We manually add the request alias name to the cypress request object,
   * in this way we can distinguish one request from another
   *
   * @link https://docs.cypress.io/guides/end-to-end-testing/working-with-graphql#Alias-multiple-queries-or-mutations
   *
   * @param req the already sent cypress request object
   * @param operationName for example 'FindCurrentUser'
   * @param action
   * @private
   */
  private static setAlias(req: CyHttpMessages.IncomingRequest, operationName: string, action: RequestTypes): void {

    if (action === RequestTypes.COMMAND || action === RequestTypes.QUERY) {
      if (RequestHandler.hasCorrectAction(req, action) && RequestHandler.hasCorrectOperationName(req, operationName, action)) {
        req.alias = operationName;
      }
    }

    // Custom requests do not contain in body the string 'command' or 'query' so we make no checks
    if (action === RequestTypes.REQUEST) {
        req.alias = operationName;
    }
  }

  /**
   * Before giving an alias name for the request, we use this method to one more time check if we assign the alias to
   * the correct request. We wait until the request is done, then we add our alis to that request.
   * req argument here comes from cypress and includes all the data from request. If everything is correct, then our
   * alias must be in request's body. For example, for queries request's body looks like this:
   *
   * body:
   *   arguments: []
   *   query: "FindLanguages"
   *
   * therefore, we first check if the request type is correct (does it have "query"), and then we check if it has
   * the correct alias name "FindLanguages"
   *
   * @param req
   * @param operationName example FindContentSnippetTree
   * @param action RequestTypes
   * @private
   */
  private static hasCorrectOperationName(req: CyHttpMessages.IncomingRequest, operationName: string, action: RequestTypes): boolean {
    const { body } = req;
    return body[action] === operationName;
  }

  private static hasCorrectAction(req: CyHttpMessages.IncomingRequest, action: RequestTypes): boolean {
    const { body } = req;
    return Object.prototype.hasOwnProperty.call(body, action);
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
      RequestHandler.intercept(request);
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
   * Creates an aliases array
   *
   * this is needed for cypress "wait" method as argument in interceptions
   *
   * Example return: ['@FindCurrentUser', '@FindLanguages', '@messagesIsGranted']
   *
   * @param requests
   */
  public static getAliasesFromRequests(requests: IRequestData[]): string[] {
    return requests.map((data) => RequestHandler.toAliasName(data.alias));
  }

  public static hasResponseError(interception: Interception): boolean {
    const { response } = interception;
    return response.body.message.error;
  }
}
