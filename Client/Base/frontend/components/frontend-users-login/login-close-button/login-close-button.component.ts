import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';

@Component({
  selector: 'apto-login-close-button',
  templateUrl: './login-close-button.component.html',
  styleUrls: ['./login-close-button.component.scss']
})
export class LoginCloseButtonComponent implements OnInit {
  @Input()
  public inner = false;
  @Output()
  public clicked = new EventEmitter();

  constructor() { }

  public ngOnInit(): void {}
}
