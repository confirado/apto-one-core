import { Component } from '@angular/core';
import { SaveDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/save-dialog/save-dialog.component';
import { ShareDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/share-dialog/share-dialog.component';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';

@Component({
	selector: 'apto-sidebar-summary-button',
	templateUrl: './sidebar-summary-button.component.html',
	styleUrls: ['./sidebar-summary-button.component.scss'],
})
export class SidebarSummaryButtonComponent {
	public constructor(
    private dialogService: DialogService,
  ) {}

	public save(): void {
    this.dialogService.openCustomDialog(SaveDialogComponent, DialogSizesEnum.md)
  }

	public share(): void {
    this.dialogService.openCustomDialog(ShareDialogComponent, DialogSizesEnum.md)
	}
}
