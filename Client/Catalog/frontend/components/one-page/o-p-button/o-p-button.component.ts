import { Component } from '@angular/core';
import { ShareDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/share-dialog/share-dialog.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { SaveDialogComponent } from '@apto-catalog-frontend-save-dialog';
import {
  addAnonymousConfiguration,
  addAnonymousConfigurationSuccess,
} from '@apto-catalog-frontend-configuration-actions';
import { Store } from '@ngrx/store';
import { Actions, ofType } from '@ngrx/effects';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { v4 as uuidv4 } from 'uuid';

@UntilDestroy()
@Component({
	selector: 'apto-o-p-button',
	templateUrl: './o-p-button.component.html',
	styleUrls: ['./o-p-button.component.scss'],
})
export class OPButtonComponent {
  private lastConfigurationId: string;

	public constructor(
    private dialogService: DialogService,
    private store: Store,
    private readonly actions$: Actions
  ) {
    this.showModalOnAddAnonymousConfigurationSuccess();
  }

	public save(): void {
    this.dialogService.openCustomDialog(SaveDialogComponent, DialogSizesEnum.md);
	}

  private generateConfigurationId(): string {
    this.lastConfigurationId = uuidv4();
    return this.lastConfigurationId;
  }

	public share(): void {
    this.store.dispatch(addAnonymousConfiguration(
      {
        payload: {
          id: this.generateConfigurationId()
        }
      }));
	}

  private showModalOnAddAnonymousConfigurationSuccess(): void {
    this.actions$.pipe(
      ofType(addAnonymousConfigurationSuccess),
      untilDestroyed(this)
    ).subscribe((result) => {
      const data = {
        configurationId: this.lastConfigurationId,
        configurationType: 'anonymous'
      };
      this.dialogService.openCustomDialog(ShareDialogComponent, DialogSizesEnum.md, data);
    });
  }
}
