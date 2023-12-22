export interface AptoTableColumn {
  prop: string,
  name: string,
  index?: boolean, /*index columns should have this name*/
  sortable?: boolean,
  styles?: { [key: string]: string }, // CSSStyleDeclaration
}

