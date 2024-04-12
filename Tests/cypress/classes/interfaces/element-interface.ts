import { Select } from '../common/elements/form/select';
import { Checkbox } from '../common/elements/form/checkbox';
import { Input } from '../common/elements/form/input';
import { Textarea } from '../common/elements/form/textarea';
import { MediaSelect } from '../common/elements/custom/media-select';
import { TranslatedValue } from '../common/elements/custom/translated-value';
import Chainable = Cypress.Chainable;

export type ElementType = Checkbox | Input | Select | Textarea | MediaSelect | TranslatedValue;

export interface Attributes {
  [key: string]: string;
}

export interface ElementInterface {
  get(selector: string): ElementType;
  getByAttr(selector: string): ElementType;
  set(selector: Chainable<JQuery<HTMLElement>>): ElementType;

  hasLabel?(label: string): ElementType;
  hasNotLabel?(label: string): ElementType;
  hasValue?(value: any): ElementType;
  hasNotValue?(value: any): ElementType;
  hasError?(): ElementType;
  hasNotError?(): ElementType;
  attributes?(attributes: any): ElementType;

  ////////////////////////////////
  // optional methods
  ////////////////////////////////

  // checkbox
  isChecked?(state: boolean): ElementType;
  isUnChecked?(state: boolean): ElementType;
  check?(): ElementType;
  unCheck?(): ElementType;
  click?(): ElementType;

  // input
  getValue?(): ElementType;
  writeValue?(value: string | number | string[]): ElementType;

  // select
  isSelected?(): ElementType;
  isNotSelected?(): ElementType;
  select?(value: string): ElementType;

}
