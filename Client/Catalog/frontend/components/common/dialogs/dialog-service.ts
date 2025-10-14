import { Injectable } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { ConfirmationDialogComponent } from '@apto-catalog-frontend-confirmation-dialog';

@Injectable()
export class DialogService {
  dialogTypesEnum = DialogTypesEnum;

  constructor(
    private matDialog: MatDialog,
  ) { }

  /**
   * example call:
   *
   * openCustomDialog(ConfirmationDialogComponent, DialogSizesEnum.lg, {}, {})
   *
   * some data to the dialog can be passed either from it's instance like:
   *    const dialogRef = this.dialogService.openCustomDialog(WidthHeightElementComponent, this.dialogSizesEnum.lg);
   *    const instance = dialogRef.componentInstance;
   *    instance.element = element;
   *    instance.product = product;
   *
   * or passing a 3-rd argument to the dialog method
   *
   * we add and extra "data" property to passed data as it is the requirement from angular:
   *
   * @link https://material.angular.io/components/dialog/overview#sharing-data-with-the-dialog-component
   *
   * @param component
   * @param size
   * @param data our custom data we want to apss to the dialog
   * @param options angular material options
   */
  openCustomDialog(component: any, size: DialogSizesEnum, data?: any, options?: MatDialogConfig): MatDialogRef<any> {
    return this.matDialog.open(component, { ...{...options, ...{width: size}}, ...{data: {...data, ...{size: size}}}});
  }

  openConfirmationDialog(size: DialogSizesEnum, title?: string, descriptionText?: string, cancelButtonText?: string, confirmButtonText?: string) {
    return this.openCustomDialog(
      ConfirmationDialogComponent,
      size,
      {title, descriptionText, cancelButtonText, confirmButtonText, type: this.dialogTypesEnum.CONFIRM},
      {width: size}
    );
  }

  openErrorDialog(size: DialogSizesEnum, title?: string, descriptionText?: string, cancelButtonText?: string, confirmButtonText?: string) {
    return this.openCustomDialog(
      ConfirmationDialogComponent,
      size,
      {title, descriptionText, cancelButtonText, confirmButtonText, type: this.dialogTypesEnum.ERROR},
      {width: size}
    );
  }

  openWarningDialog(size: DialogSizesEnum, title?: string, descriptionText?: string, cancelButtonText?: string, confirmButtonText?: string) {
    return this.openCustomDialog(
      ConfirmationDialogComponent,
      size,
      {title, descriptionText, cancelButtonText, confirmButtonText, type: this.dialogTypesEnum.WARNING},
      {width: size}
    );
  }

  openInfoDialog(size: DialogSizesEnum, title?: string, descriptionText?: string, cancelButtonText?: string, confirmButtonText?: string) {
    return this.openCustomDialog(
      ConfirmationDialogComponent,
      size,
      {title, descriptionText, cancelButtonText, confirmButtonText, type: this.dialogTypesEnum.INFO},
      {width: size}
    );
  }
}
