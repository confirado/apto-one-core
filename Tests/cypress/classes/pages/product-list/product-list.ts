import { Page } from '@apto-catalog-frontend/models/pagination';
import { Queries } from '../../message-bus/queries';
import { IRequestData } from '../../models';
import { IPage } from '../../interfaces/page-interface';

export interface IProductListResponse extends Page<any>{}

export class ProductList implements IPage {

  public static visit(): void {
    cy.visit(Cypress.env('baseUrl'));
  }

  public static isCorrectPage(): void {
    cy.get('apto-product-list').should('exist');
  }

  public static isCorrectPageContent(): void { }

  public static get initialQueryList(): IRequestData[] {
    return [
      Queries.FindShopContext,
      Queries.FindContentSnippetTree,
      Queries.FindProductsByFilter,
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
      ...ProductList.initialQueryList,
      ...ProductList.initialCommandList,
      ...ProductList.initialCustomRequestList,
    ];
  }
}
