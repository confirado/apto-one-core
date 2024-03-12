import { IRequestData } from '../models';

export class Shopware {

  public static get endpoint(): string {
    return 'shopware65/public/template/AptoConnector';
  }

  public static get getState(): IRequestData {
    return {
      alias: 'getState',
      payload: { query: 'getState' },
      endpoint: Shopware.endpoint,
    };
  }
}
