import { HttpRequestTypes, IRequestData, RequestTypes } from '../models';
import { AddPropertiesToReturnValueForClass } from '../decorators/decorators';

@AddPropertiesToReturnValueForClass(['endpoint', 'method', 'type'])
export class Commands {
  public static endpoint = 'message-bus/command';
  public static method = HttpRequestTypes.POST;
  public static type = RequestTypes.COMMAND;

  // ... commands here

  public static get AddProduct(): IRequestData {
    return {
      alias: 'AddProduct',
      payload: {
        command: 'AddProduct',
        arguments: {
          0: null,
          1: {de_DE: 'name of the product'}, // todo import from fixture or so the name , use the same
          2: {},
          3: {}, // this is not empty
          4: [],
          5: false,
          6: false,
          7: false,
          8: "",
          9: {},
          10: {},
          11: 0,
          12: "",
          13: 0,
          14: 0,
          15: "",
          16: "Apto\\Catalog\\Application\\Core\\Service\\PriceCalculator\\SimplePriceCalculator",
          17: 0,
          18: 0,
          19: null,
          20: 10,
          21: true,
        }
      },
    };
  }

  public static get RemoveProduct(): IRequestData {
    return {
      alias: 'RemoveProduct',
      payload: {
        command: 'RemoveProduct',
        arguments: { }
      },
    };
  }
}
