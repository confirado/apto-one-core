import { Component } from '@angular/core';
import { SaveDialogComponent } from '@apto-catalog-frontend-save-dialog';
import { ShareDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/share-dialog/share-dialog.component';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import { Store } from "@ngrx/store";

@Component({
	selector: 'apto-sidebar-summary-button',
	templateUrl: './sidebar-summary-button.component.html',
	styleUrls: ['./sidebar-summary-button.component.scss'],
})

export class SidebarSummaryButtonComponent {
  public readonly aptoSliderAction$ = this.store.select(selectContentSnippet('aptoSliderAction'));
	public readonly shareLinks$ = this.store.select(selectContentSnippet('aptoSliderAction.shareLinks'));
  public socialLinksCount: number = 0;
  public constructor(
    private dialogService: DialogService,
    private store: Store,
  ) {
    this.shareLinks$.subscribe((next) => {
      this.socialLinksCount = 0;
      if (next !== null) {
        this.socialLinksCount = next.children.length
      }
    });
  }

	public save(): void {
    this.dialogService.openCustomDialog(SaveDialogComponent, DialogSizesEnum.md)
  }

	public share(): void {
    this.dialogService.openCustomDialog(ShareDialogComponent, DialogSizesEnum.md)
	}
}
