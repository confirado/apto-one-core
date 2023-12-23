import { HttpRequestTypes, RequestTypes } from './Models';
import { AddPropertiesToReturnValueForClass } from './decorators';

@AddPropertiesToReturnValueForClass(['endpoint', 'method', 'type'])
export class Commands {
  public static endpoint = 'message-bus/command';
  public static method = HttpRequestTypes.POST;
  public static type = RequestTypes.COMMAND;

  // ... commands here
}
