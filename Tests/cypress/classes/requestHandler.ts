import { CustomInterceptionResponse } from './Models';
import { CyHttpMessages, Interception } from 'cypress/types/net-stubbing';
import { Queries } from './queries';
import { Commands } from './commands';

// eslint-disable-next-line no-shadow
export enum RequestTypes {
  QUERY = 'query',
  COMMAND = 'command',
}

export class RequestHandler {

  public static interceptQuery(aliasName: string): void {
    cy.intercept('POST', `**/${Queries.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, aliasName, RequestTypes.QUERY);
    });
  }

  public static interceptCommand(aliasName: string): void {
    cy.intercept('POST', `**/${Commands.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, aliasName, RequestTypes.COMMAND);
    });
  }

  /**
   * Sets alias name for the query
   *
   * @link https://docs.cypress.io/guides/end-to-end-testing/working-with-graphql#Alias-multiple-queries-or-mutations
   *
   * @param req
   * @param operationName
   * @param action
   * @private
   */
  private static setAlias(req: CyHttpMessages.IncomingRequest, operationName: string, action: RequestTypes): void {
    if (RequestHandler.hasOperationName(req, operationName, action)) {
      req.alias = operationName;
    }
  }

  /**
   * Checks that the command or the query it one we are looking for
   *
   * example check: body.query = FindContentSnippetTree
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

  public static hasResponseError(interception: Interception<CustomInterceptionResponse<any>>): boolean {
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
