import { Component, Input, OnInit } from '@angular/core';
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import { Store } from '@ngrx/store';


@Component({
	selector: 'apto-sidebar-summary-progress',
	templateUrl: './sidebar-summary-progress.component.html',
	styleUrls: ['./sidebar-summary-progress.component.scss'],
})
export class SidebarSummaryProgressComponent implements OnInit {
	@Input() progress: number | undefined | null;
  public readonly summaryProgress$ = this.store.select(selectContentSnippet('summaryProgress'));

	constructor(private store: Store) {}

	ngOnInit(): void {}
}
