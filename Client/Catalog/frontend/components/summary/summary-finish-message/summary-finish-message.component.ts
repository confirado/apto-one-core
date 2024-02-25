import { Component, OnInit } from '@angular/core';
import { Store } from "@ngrx/store";
import { selectContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";


@Component({
  selector: 'apto-summary-finish-message',
  templateUrl: './summary-finish-message.component.html',
  styleUrls: ['./summary-finish-message.component.scss']
})
export class SummaryFinishMessageComponent implements OnInit {
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  constructor(private store: Store) { }

  ngOnInit(): void {
  }

}
