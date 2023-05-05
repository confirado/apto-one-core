import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA} from '@angular/material/dialog';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { DialogDataInterface } from '@apto-catalog-frontend/components/common/dialogs/dialog-data-interface';

@Component({
  selector: 'apto-confirmation-dialog',
  templateUrl: './confirmation-dialog.component.html',
  styleUrls: ['./confirmation-dialog.component.scss']
})
export class ConfirmationDialogComponent implements OnInit {

  dialogTypesEnum = DialogTypesEnum;

  constructor(
    @Inject(MAT_DIALOG_DATA) public data: DialogDataInterface,
  ) { }

  ngOnInit(): void {
  }
}
