import { IRequestData } from '../../models';
import { IPage } from '../../interfaces/page-interface';
import { CryptoService } from '@apto-base-core/services/crypto-service';
import { Queries } from '../../message-bus/queries';
import { Commands } from '../../message-bus/commands';

export class Sections implements IPage {

  public static visit(visitByClick = false): void {  }

  public static isCorrectPage(): void {  }

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
    return [];
  }

  public static generateName(characterCount: number = 8) {
    return 'section-' + CryptoService.generateRandomString(characterCount);
  }

  public static get addSectionRequests(): IRequestData[] {
    return [
      Commands.AddProductSection,
      Queries.FindProductSections,
    ];
  }

  public static get editSectionQueryList(): IRequestData[] {
    return [
      Queries.FindSection,
      Queries.FindSectionElements,
      Queries.FindSectionPrices,
      Queries.FindSectionDiscounts,
      Queries.FindSectionCustomProperties,
      Queries.FindUsedCustomPropertyKeys,
      Queries.FindGroups,
    ];
  }

  public static createSection() {

  }
}
