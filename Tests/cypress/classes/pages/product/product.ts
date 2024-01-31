import { Queries } from '../../queries';
import { IRequestData } from '../../Models';
import { IPage } from '../../interfaces/page-interface';
import { SIDEBAR_LEFT_ITEMS } from '../../../_support/constants/constants';
import { Backend } from '../../common/backend';

export class Product implements IPage {

  public static visit(): void {
    // Backend.visit('product');

    const parent = 'sidebar-left_' + SIDEBAR_LEFT_ITEMS?.katalog.labal;
    const sub = 'sidebar-left_sub_' + SIDEBAR_LEFT_ITEMS?.katalog.subItems.produkte.label;

    Backend.leftMenuItemClick(parent, sub);
  }

  public static isCorrectPage(): void {
    cy.get('apto-page-header').should('exist');
    cy.get('apto-page-header .md-toolbar-tools').should('exist');
    cy.dataCy('header_product-title').should('contain.text', 'Produkte');
  }

  public static isCorrectPageContent(): void { }

  public static get initialQueryList(): IRequestData[] {
    return [
      Queries.FindProductsByFilterPagination,
      Queries.FindCategories,
    ];
  }

  public static get initialCommandList(): IRequestData[] {
    return [];
  }

  public static get initialCustomRequestList(): IRequestData[] {
    return [];
  }

  public static get initialRequests(): IRequestData[] {
    return [
      ...Product.initialQueryList,
      ...Product.initialCommandList,
      ...Product.initialCustomRequestList,
    ];
  }

  public static get addProductQueryList(): IRequestData[] {
    return [
      Queries.FindFilterProperties,
      Queries.FindCategoryTree,
      Queries.FindCustomerGroups,
      Queries.FindPriceCalculators,
      Queries.FindShops,
      Queries.FindNextAvailablePosition,
    ];
  }
}
