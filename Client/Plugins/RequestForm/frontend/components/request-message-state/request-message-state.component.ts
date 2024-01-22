import {Component, Input, OnInit} from '@angular/core';

@Component({
  selector: 'apto-request-message-state',
  templateUrl: './request-message-state.component.html',
  styleUrls: ['./request-message-state.component.scss']
})
export class RequestMessageStateComponent implements OnInit {
  @Input()
  public state: {
    sending: boolean,
    success: boolean
    error: boolean
  };

  @Input()
  public success: {
    title: string,
    subtitle: string,
    message: string
  };

  @Input()
  public error: {
    title: string,
    subtitle: string,
    message: string
  };

  constructor() { }

  ngOnInit(): void {
    console.error(this.success)
  }

}
