import { IRequestData } from './Models';

export class Queries {

  public static endpoint = 'message-bus/query';

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
}
