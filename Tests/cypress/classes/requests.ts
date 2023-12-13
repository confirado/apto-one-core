import { HttpRequestTypes, IRequestData } from './Models';

/*
  Here we write custom requests that are neither query or command
*/
export class Requests {

  /**
   * Called on login page right after successfull login
   */
  public static get messagesIsGrantedRequest(): IRequestData {
    return {
      alias: 'messagesIsGranted',
      method: HttpRequestTypes.POST,
      endpoint: '**/message-bus/messages-is-granted',
    };
  }

  // todo maybe for each one write acceptance criteria right here
}
