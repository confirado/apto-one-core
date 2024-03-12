import { IRequestData } from '../../models';
import { IPage } from '../../interfaces/page-interface';
import { Product } from '../product/product';
import { CryptoService } from '@apto-base-core/services/crypto-service';
import { Commands } from '../../message-bus/commands';
import { Queries } from '../../message-bus/queries';

export class Rules implements IPage {

  public static visit(visitByClick = false): void {
    Product.visit(visitByClick);
  }

  public static isCorrectPage(): void {
    Product.isCorrectPage()
  }

  public static isCorrectPageContent(): void { }

  public static get initialQueryList(): IRequestData[] {
    return [];
  }

  public static get initialCommandList(): IRequestData[] {
    return [];
  }

  public static get initialCustomRequestList(): IRequestData[] {
    return [];
  }

  public static get initialRequests(): IRequestData[] {
    return Product.initialRequests;
  }

  public static get createRuleRequests(): IRequestData[] {
    return [
      Commands.AddProductRule,
      Queries.FindProductRules
    ];
  }

  public static get editRuleRequests(): IRequestData[] {
    return [
      Queries.FindRule,
      Queries.FindProductSectionsElements,
      Queries.FindRuleConditions,
      Queries.FindRuleImplications,
    ];
  }

  public static generateName(characterCount: number = 8) {
    return 'rules-' + CryptoService.generateRandomString(characterCount);
  }
}
