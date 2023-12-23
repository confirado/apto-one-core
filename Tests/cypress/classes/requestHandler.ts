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
   * Creates aliases array
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

  public static hasResponseError(interception: Interception): boolean {
    const { response } = interception;
    return response.body.message.error;
  }
}
