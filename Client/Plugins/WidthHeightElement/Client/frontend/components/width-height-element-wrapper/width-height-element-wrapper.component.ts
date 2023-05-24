import { Component, Input } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { HeightWidthProperties, Product } from '@apto-catalog-frontend/store/product/product.model';
import { WidthHeightElementComponent } from '../width-height-element/width-height-element.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';

@Component({
  selector: 'apto-width-height-element-wrapper',
  templateUrl: './width-height-element-wrapper.component.html',
  styleUrls: ['./width-height-element-wrapper.component.scss'],
})
export class WidthHeightElementWrapperComponent {
  @Input()
  public element: ProgressElement<HeightWidthProperties> | undefined | null;

  @Input()
  public product: Product | null | undefined;

  dialogSizesEnum = DialogSizesEnum;

  public constructor(
    private matDialog: MatDialog,
    private dialogService: DialogService,
  ) {
  }

  public openDialog(element: ProgressElement, product: Product): void {
    const dialogRef = this.dialogService.openCustomDialog(WidthHeightElementComponent, this.dialogSizesEnum.lg);

    const instance = dialogRef.componentInstance;
    instance.element = element;
    instance.product = product;
    instance.isDialog = true;
  }
}
