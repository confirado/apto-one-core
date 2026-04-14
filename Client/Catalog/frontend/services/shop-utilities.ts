import { environment } from '@apto-frontend/src/environments/environment';

export function getShowGross(initShopSuccessResult: any): boolean {
  const aptoBase: any = initShopSuccessResult.aptoBase;
  if (aptoBase.shop && aptoBase.shop.connector && aptoBase.shop.connector.user && aptoBase.shop.connector.customerGroup) {
    return aptoBase.shop.connector.customerGroup.showGross;
  }
  else if (aptoBase.frontendUser && aptoBase.frontendUser.currentUser && aptoBase.frontendUser.currentUser.customerGroup) {
    return aptoBase.frontendUser.currentUser.customerGroup.showGross;
  }
  else {
    return environment.defaultCustomerGroup.showGross;
  }
}
