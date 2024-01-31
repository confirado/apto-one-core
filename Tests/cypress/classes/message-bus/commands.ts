import { HttpRequestTypes, RequestTypes } from '../models';
import { AddPropertiesToReturnValueForClass } from '../decorators/decorators';

@AddPropertiesToReturnValueForClass(['endpoint', 'method', 'type'])
export class Commands {
  public static endpoint = 'message-bus/command';
  public static method = HttpRequestTypes.POST;
  public static type = RequestTypes.COMMAND;

  // ... commands here
}
