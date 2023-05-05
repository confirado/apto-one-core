import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';

@Component({
  selector: 'apto-close-button',
  templateUrl: './close-button.component.html',
  styleUrls: ['./close-button.component.scss']
})
export class CloseButtonComponent implements OnInit {

  @Input() inner = false;
  @Output() clicked = new EventEmitter();

  constructor() { }

  ngOnInit(): void {}
}
