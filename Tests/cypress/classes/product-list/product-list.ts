import { Page } from '@apto-catalog-frontend/models/pagination';
import { Queries } from '../queries';
import { IRequestData } from '../Models';

export interface IProductListResponse extends Page<any>{}

export class ProductList {

  public static visit(): void {
    cy.visit(Cypress.env('baseUrl'));
  }

  public static get initialRequestList(): IRequestData[] {
    return [
      Queries.FindShopContext,
      Queries.FindContentSnippetTree,
      Queries.FindProductsByFilter,
    ];
  }

  public static get initialAliasList(): string[] {
    return ProductList.initialRequestList.map((data) => `@${data.alias}`);
  }
}
