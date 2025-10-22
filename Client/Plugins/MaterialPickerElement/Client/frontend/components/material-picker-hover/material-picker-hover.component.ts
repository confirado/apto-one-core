import { Component, HostListener, Input, OnInit } from '@angular/core';
import { Material, MaterialPickerItem } from '@apto-catalog-frontend/models/material-picker';
import { ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectProgressState } from '@apto-catalog-frontend-configuration-selectors';
import { Store } from '@ngrx/store';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { MaterialPickerDetailsPopupComponent } from '@apto-material-picker-element-frontend/components/material-picker-details-popup/material-picker-details-popup.component';

@Component({
  selector: 'apto-material-picker-hover',
  templateUrl: './material-picker-hover.component.html',
  styleUrls: ['./material-picker-hover.component.scss']
})
export class MaterialPickerHoverComponent implements OnInit {

  @Input() poolItem: MaterialPickerItem;
  @Input() element: ProgressElement;

  public mediaUrl = environment.api.media + '/';
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));
  public readonly steps$ = this.store.select(selectProgressState);

  constructor(
    private store: Store,
    private dialogService: DialogService,
  ) { }

  ngOnInit(): void {

  }

  @HostListener('click', ['$event'])
  onClick(event: any) {
    event.stopPropagation();
  }

  showDetails() {
    const dialogRef = this.dialogService.openCustomDialog(MaterialPickerDetailsPopupComponent, DialogSizesEnum.lg);
    const instance = dialogRef.componentInstance;

    instance.data = {
      element: this.element,
      poolItem: this.poolItem,
    };
  }

  isFirstGalleryImageInHover(material?: any) {
    return false;
  }

  isPreviewImageInHover(material? :any) {
    return true;
  }

  hasMaterialPropertyIcons() {
    return true;
  }

  hasMaterialLightProperties(material: Material) {
    return (null !== material.reflection || null !== material.transmission || null !== material.absorption);
  }

  setValues(materialId: any, materialName: any, priceGroup: any) {

  }

  isPoolItemSelected(poolItem: MaterialPickerItem, steps: any) {
    return false;
  }

  removeMaterial(materialId: string) {
    return false;
  }
}
