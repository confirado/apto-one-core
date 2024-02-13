import { HttpRequestTypes, IRequestData, RequestTypes } from '../models';
import { AddPropertiesToReturnValueForClass } from '../decorators/decorators';

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

  public static get FindProductsByFilterPagination(): IRequestData {
    return {
      alias: 'FindProductsByFilterPagination',
      payload: { query: 'FindProductsByFilterPagination' },
    };
  }

  public static get FindCategories(): IRequestData {
    return {
      alias: 'FindCategories',
      payload: { query: 'FindCategories' },
    };
  }

  public static get FindFilterProperties(): IRequestData {
    return {
      alias: 'FindFilterProperties',
      payload: { query: 'FindFilterProperties' },
    };
  }

  public static get FindCategoryTree(): IRequestData {
    return {
      alias: 'FindCategoryTree',
      payload: { query: 'FindCategoryTree' },
    };
  }

  public static get FindCustomerGroups(): IRequestData {
    return {
      alias: 'FindCustomerGroups',
      payload: { query: 'FindCustomerGroups' },
    };
  }

  public static get FindPriceCalculators(): IRequestData {
    return {
      alias: 'FindPriceCalculators',
      payload: { query: 'FindPriceCalculators' },
    };
  }

  public static get FindShops(): IRequestData {
    return {
      alias: 'FindShops',
      payload: { query: 'FindShops' },
    };
  }

  public static get FindNextAvailablePosition(): IRequestData {
    return {
      alias: 'FindNextAvailablePosition',
      payload: { query: 'FindNextAvailablePosition' },
    };
  }

  public static get FindProducts(): IRequestData {
    return {
      alias: 'FindProducts',
      payload: { query: 'FindProducts' },
    };
  }

  public static get FindProduct(): IRequestData {
    return {
      alias: 'FindProduct',
      payload: { query: 'FindProduct' },
    };
  }

  public static get FindProductSections(): IRequestData {
    return {
      alias: 'FindProductSections',
      payload: { query: 'FindProductSections' },
    };
  }

  public static get FindProductRules(): IRequestData {
    return {
      alias: 'FindProductRules',
      payload: { query: 'FindProductRules' },
    };
  }

  public static get FindProductComputedValues(): IRequestData {
    return {
      alias: 'FindProductComputedValues',
      payload: { query: 'FindProductComputedValues' },
    };
  }

  public static get FindProductPrices(): IRequestData {
    return {
      alias: 'FindProductPrices',
      payload: { query: 'FindProductPrices' },
    };
  }

  public static get FindProductDiscounts(): IRequestData {
    return {
      alias: 'FindProductDiscounts',
      payload: { query: 'FindProductDiscounts' },
    };
  }

  public static get FindProductCustomProperties(): IRequestData {
    return {
      alias: 'FindProductCustomProperties',
      payload: { query: 'FindProductCustomProperties' },
    };
  }

  public static get FindUsedCustomPropertyKeys(): IRequestData {
    return {
      alias: 'FindUsedCustomPropertyKeys',
      payload: { query: 'FindUsedCustomPropertyKeys' },
    };
  }
}
