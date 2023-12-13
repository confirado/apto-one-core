import { HttpRequestTypes } from './Models';

export class Commands {
  public static endpoint = 'message-bus/command';
  public static method = HttpRequestTypes.POST;
}
