import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';

export interface DialogDataInterface {
  size?: DialogSizesEnum,
  title?: string,
  descriptionText?: string,
  buttonText?: string,
  secondButtonText?: string,
  type?: DialogTypesEnum
}
