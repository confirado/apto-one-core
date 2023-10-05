export interface ItemsUpdatePayload {
  poolId: string,
  filter: ItemsUpdateFilter
}

export interface ItemsUpdateFilter {
  searchString: string,
  colorRating: string,
  priceGroup: string,
  properties: string[],
  orderBy: string,
}
