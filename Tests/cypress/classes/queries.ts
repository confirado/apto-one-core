import { HttpRequestTypes, IRequestData, RequestTypes } from './Models';
import { AddPropertiesToReturnValueForClass } from './decorators';

@AddPropertiesToReturnValueForClass(['endpoint', 'method', 'type'])
export class Queries {
  public static endpoint = 'message-bus/query';
  public static method = HttpRequestTypes.POST;
  public static type = RequestTypes.QUERY;

  public static get FindShopContext(): IRequestData {
    return {
      alias: 'FindShopContext',
      payload: { query: 'FindShopContext' },
    };
  }

  public static get FindContentSnippetTree(): IRequestData {
    return {
      alias: 'FindContentSnippetTree',
      payload: { query: 'FindContentSnippetTree' },
    };
  }

  public static get FindProductsByFilter(): IRequestData {
    return {
      alias: 'FindProductsByFilter',
      payload: { query: 'FindProductsByFilter' },
    };
  }

  public static get FindCurrentUser(): IRequestData {
    return {
      alias: 'FindCurrentUser',
      payload: { query: 'FindCurrentUser' },
    };
  }

  public static get FindLanguages(): IRequestData {
    return {
      alias: 'FindLanguages',
      payload: { query: 'FindLanguages' },
    };
  }
}
