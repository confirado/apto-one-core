import { Component, Input, OnInit } from '@angular/core';

@Component({
	selector: 'apto-sidebar-summary-progress',
	templateUrl: './sidebar-summary-progress.component.html',
	styleUrls: ['./sidebar-summary-progress.component.scss'],
})
export class SidebarSummaryProgressComponent implements OnInit {
	@Input() progress: number | undefined | null;

	constructor() {}

	ngOnInit(): void {}
}
