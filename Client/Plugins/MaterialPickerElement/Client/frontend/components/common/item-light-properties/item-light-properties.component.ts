import { Component, Input, OnInit } from '@angular/core';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';

@Component({
  selector: 'apto-item-light-properties',
  templateUrl: './item-light-properties.component.html',
  styleUrls: ['./item-light-properties.component.scss']
})
export class ItemLightPropertiesComponent implements OnInit {

  @Input() reflection: number|null;
  @Input() transmission: number|null;
  @Input() absorption: number|null;

  public readonly clientUrl = environment.api.client + '/';
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));

  constructor(public store: Store) { }

  ngOnInit(): void {
  }
}
