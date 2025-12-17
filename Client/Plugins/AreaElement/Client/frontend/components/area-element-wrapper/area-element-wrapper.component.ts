import { Component, Input } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AreaElementDefinitionProperties, ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { AreaElementComponent } from '@element-definition-area-element';

@Component({
  selector: 'apto-area-element-wrapper',
  templateUrl: './area-element-wrapper.component.html',
  styleUrls: ['./area-element-wrapper.component.scss'],
})
export class AreaElementWrapperComponent {
  @Input()
  public element: ProgressElement<AreaElementDefinitionProperties> | undefined | null;

  @Input()
  public section: Section | null | undefined;

  @Input()
  public product: Product | null | undefined;

  dialogSizesEnum = DialogSizesEnum;

  public constructor(
    private matDialog: MatDialog,
    private dialogService: DialogService,
  ) {
  }

  public openDialog(element: ProgressElement, product: Product): void {
    const dialogRef = this.dialogService.openCustomDialog(AreaElementComponent, this.dialogSizesEnum.lg);

    const instance = dialogRef.componentInstance;
    instance.element = element;
    instance.product = product;
    instance.isDialog = true;
  }
}
