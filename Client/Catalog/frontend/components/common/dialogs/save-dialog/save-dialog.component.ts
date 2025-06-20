import { Component } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import { addGuestConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { Store } from '@ngrx/store';
import { selectContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import { v4 as uuidv4 } from 'uuid';

@Component({
	selector: 'apto-save-dialog',
	templateUrl: './save-dialog.component.html',
	styleUrls: ['./save-dialog.component.scss'],
})
export class SaveDialogComponent {
	public formGroup = new FormGroup({
		email: new FormControl<string>('', { nonNullable: true }),
		name: new FormControl<string>('', { nonNullable: true }),
    id: new FormControl<string>(uuidv4(), { nonNullable: true })
	});
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('AptoGuestConfigurationDialog'));

	public constructor(private dialogRef: MatDialogRef<SaveDialogComponent>, private store: Store) {}

	public onSubmit(): void {
		this.store.dispatch(addGuestConfiguration({ payload: this.formGroup.getRawValue() }));
		this.dialogRef.close();
	}
}
