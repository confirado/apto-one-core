import { Component, Input, OnInit } from '@angular/core';

export enum LoadingIndicatorTypes {
  GLOBAL = 'global',
  ELEMENT = 'element'
}

@Component({
  selector: 'apto-loading-indicator',
  templateUrl: './loading-indicator.component.html',
  styleUrls: ['./loading-indicator.component.scss'],
})
export class LoadingIndicatorComponent implements OnInit {

  /**
   * if 'element' option taken, then the parent element must be positioned with 'position: relative'
   */
  @Input()
  public type = LoadingIndicatorTypes.GLOBAL;

  public ngOnInit(): void { }
}
