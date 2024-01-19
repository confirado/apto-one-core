import { Component, OnInit, Input } from '@angular/core';
import { Store } from '@ngrx/store';
import { NgForOf, NgIf } from '@angular/common';
import { AptoTableColumn } from '@apto-catalog-frontend/components/common/apto-table/apto-table-interfaces';
import { JoinStylesPipe } from '@apto-base-frontend/pipes/join-styles.pipe';

@Component({
  selector: 'apto-table',
  templateUrl: './apto-table.component.html',
  styleUrls: ['./apto-table.component.scss'],
  standalone: true,
  imports: [
    NgForOf,
    NgIf,
    JoinStylesPipe,
  ],
})
export class AptoTableComponent implements OnInit {
  @Input() public rows: Array<any>;
  @Input() public columns: AptoTableColumn[];
  @Input() public caption = '';

  public constructor(
    public store: Store,
  ) { }

  public ngOnInit(): void { }
}
