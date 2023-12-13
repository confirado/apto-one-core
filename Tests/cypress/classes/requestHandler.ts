import { CustomInterceptionResponse, IRequestData } from './Models';
import { CyHttpMessages, Interception } from 'cypress/types/net-stubbing';
import { Queries } from './queries';
import { Commands } from './commands';
import { Requests } from './requests';

// eslint-disable-next-line no-shadow
export enum RequestTypes {
  QUERY = 'query',
  COMMAND = 'command',
  REQUEST = 'request',
}

export class RequestHandler {

  public static interceptQuery(aliasName: string): void {
    cy.intercept(Queries.method, `**/${Queries.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, aliasName, RequestTypes.QUERY);
    });
  }

  public static interceptCommand(aliasName: string): void {
    cy.intercept(Commands.method, `**/${Commands.endpoint}*`, (req: CyHttpMessages.IncomingRequest) => {
      RequestHandler.setAlias(req, aliasName, RequestTypes.COMMAND);
    });
  }

  public static interceptRequest(request: IRequestData): void {
    cy.intercept(request.method, request.endpoint).as(request.alias);
  }

  public static toAliasName(alias: string): string {
    return `@${alias}`;
  }

  public static registerInterceptions(list: IRequestData[]): void {

    // console.error('list')
    // console.log(list)

    // Login.initialQueryList.forEach((request) => RequestHandler.interceptQuery(request.alias));
    // Login.initialCommandList.forEach((request) => RequestHandler.interceptCommand(request.alias));
    // Login.initialCustomRequestList.forEach((request) => RequestHandler.interceptRequest(request));
  }

  public static getAliasListFromQueryCommandList(queryCommandList: IRequestData[]): string[] {
    return queryCommandList.map((data) => RequestHandler.toAliasName(data.alias));
  }

  public static getWaitList(): string[] {
    return [];
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
