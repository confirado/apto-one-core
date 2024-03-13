import { Component, OnInit, Input } from '@angular/core';
import { environment } from '@apto-frontend/src/environments/environment';
import { Attachments } from '@apto-catalog-frontend/store/product/product.model';

@Component({
  selector: 'apto-element-attachment',
  templateUrl: './element-attachment.component.html',
  styleUrls: ['./element-attachment.component.scss']
})
export class ElementAttachmentComponent implements OnInit {

  @Input()
  attachments: Attachments[] = [];

  protected mediaUrl = environment.api.media;

  public ngOnInit(): void {}
}
