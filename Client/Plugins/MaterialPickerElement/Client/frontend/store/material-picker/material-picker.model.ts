export interface ItemsUpdatePayload {
  poolId: string,
  filter: ItemsUpdateFilter,
  sortBy: string,
  orderBy: string,
  compressedState?: any,
}

export interface ItemsUpdateFilter {
  searchString: string,
  colorRating: string,
  priceGroup: string,
  properties: string[],
}
