import { HttpRequestTypes, IRequestData, RequestTypes } from './Models';
import { AddPropertiesToReturnValueForClass } from './decorators';

/*
  Here we write custom requests that are neither query or command
*/

@AddPropertiesToReturnValueForClass(['type'])
export class Requests {

  public static type = RequestTypes.REQUEST;

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
