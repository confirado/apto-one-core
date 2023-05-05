import { Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ShareDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/share-dialog/share-dialog.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { SaveDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/save-dialog/save-dialog.component';

@Component({
	selector: 'apto-o-p-button',
	templateUrl: './o-p-button.component.html',
	styleUrls: ['./o-p-button.component.scss'],
})
export class OPButtonComponent {
	public constructor(
    private matDialog: MatDialog,
    private dialogService: DialogService,
  ) {}

	public save(): void {
    this.dialogService.openCustomDialog(SaveDialogComponent, DialogSizesEnum.md)
	}

	public share(): void {
    this.dialogService.openCustomDialog(ShareDialogComponent, DialogSizesEnum.md)
	}
}
