import { Select } from './select';
import { Checkbox } from './checkbox';
import { Input } from './input';
import { Textarea } from './textarea';

export type ElementType = Checkbox | Input | Select | Textarea;

export interface Attributes {
  [key: string]: string;
}

export interface Element {
  getByAttr(selector: string): ElementType;
  get(selector: string): ElementType;
  hasLabel(label: string): ElementType;
  hasNotLabel(label: string): ElementType;
  hasValue(value: any): ElementType;
  hasNotValue(value: any): ElementType;
  hasError(): ElementType;
  hasNotError(): ElementType;
  attributes(attributes: any): ElementType;

  // optional methods
  checked?(state: boolean): ElementType;
  unChecked?(state: boolean): ElementType;
}
