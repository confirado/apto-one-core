import { Component, Input, OnInit } from '@angular/core';
import { Material, MaterialPickerItem } from '@apto-catalog-frontend/models/material-picker';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';

interface MaterialPickerDetailsPopupData {
  element: ProgressElement,
  poolItem: MaterialPickerItem,
}

@Component({
  selector: 'apto-material-picker-details-popup',
  templateUrl: './material-picker-details-popup.component.html',
  styleUrls: ['./material-picker-details-popup.component.scss']
})
export class MaterialPickerDetailsPopupComponent implements OnInit {

  @Input() data: MaterialPickerDetailsPopupData;

  public mediaUrl = environment.api.media + '/';
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));
  public readonly steps$ = this.store.select(selectProgressState);
  public poolItemImageSelected = '';

  constructor (
    private store: Store,
  ) { }

  ngOnInit(): void {
    this.poolItemImageSelected = this.data.poolItem.material.previewImage.fileUrl;
  }

  hasMaterialLightProperties(material: Material) {
    return (null !== material.reflection || null !== material.transmission || null !== material.absorption);
  }

  hasMaterialPropertyIcons() {

  }

  setPoolItemImage (fileUrl: string) {
    this.poolItemImageSelected = fileUrl;
  }
}
