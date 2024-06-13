export interface ItemsUpdatePayload {
  poolId: string,
  filter: ItemsUpdateFilter,
  compressedState?: any,
}

export interface ItemsUpdateFilter {
  searchString: string,
  colorRating: string,
  priceGroup: string,
  properties: string[],
  orderBy: string,
}
