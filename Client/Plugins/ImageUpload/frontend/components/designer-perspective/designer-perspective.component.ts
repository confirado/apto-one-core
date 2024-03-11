import { Component, ComponentRef, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { DesignerComponent } from '@apto-image-upload-frontend/components/designer/designer.component';
import {
  selectCurrentPerspective,
  selectPerspectives,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Store } from '@ngrx/store';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { MatButtonToggleChange } from '@angular/material/button-toggle';
import { combineLatest, Observable, take } from 'rxjs';
import {
  getConfigurationStateSuccess,
  setHideOnePage, setPerspective, updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { HttpClient } from '@angular/common/http';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';
import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { sha1 } from '@apto-base-core/helper/encrypt';
import { FabricCanvasService } from '@apto-image-upload-frontend/services/fabric-canvas.service';
import { Actions, ofType } from '@ngrx/effects';
import { resetPasswordSuccess } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';

@UntilDestroy()
@Component({
  selector: 'apto-designer-perspective',
  templateUrl: './designer-perspective.component.html',
  styleUrls: ['./designer-perspective.component.scss']
})
export class DesignerPerspectiveComponent implements OnInit {

  @ViewChild('designer', { read: ViewContainerRef, static: true }) designerContainer: ViewContainerRef;

  public currentPerspective: string;
  public perspectives: string[] = [];
  public locale: string;
  public canvas: CanvasState;
  public product: Product;

  public readonly cancelMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.cancelMessage'));
  public readonly resetMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.resetMessage'));
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));

  private designers: {id: string, component: ComponentRef<DesignerComponent>}[] = [];

  constructor(
    private store: Store,
    private http: HttpClient,
    private dialogService: DialogService,
    private fabricCanvasService: FabricCanvasService,
    private actions$: Actions
  ) {
    this.locale = environment.defaultLocale;
  }

  ngOnInit(): void {
    combineLatest([
      this.store.select(selectLocale),
      this.store.select(selectCanvas),
      this.store.select(selectPerspectives),
      this.store.select(selectCurrentPerspective),
      this.store.select(selectProduct)
    ]).pipe(take(1)).subscribe((result: [string, CanvasState, string[], string, Product]) => {
      this.locale = result[0] || environment.defaultLocale;
      this.canvas = result[1];
      this.perspectives = result[2];
      this.currentPerspective = result[3];
      this.product = result[4];
      this.createDesigners();
    });
  }

  public perspectiveChange($event: MatButtonToggleChange): void {
    this.currentPerspective = $event.value;

    this.store.dispatch(setPerspective({ perspective: this.currentPerspective }));

    for (const designer of this.designers) {
      designer.component.instance.visible = this.currentPerspective === designer.id;
    }
  }

  public async save() {
    const perspectivePayloads = {};

    for (const designer of this.designers) {
      const fabricCanvas = designer.component.instance.fabricCanvas.toJSON(['identifier', 'payload']);
      fabricCanvas.objects = [];

      designer.component.instance.fabricCanvas.getObjects().forEach((object) => {
        fabricCanvas.objects.push(object.toJSON(['identifier', 'payload']));
      });

      const date: Date = new Date();
      const fabricCanvasJson = JSON.stringify(fabricCanvas);
      const productId = this.product.id;
      const directory = '/apto-plugin-image-upload/render-images/' + date.getFullYear() + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/';
      const fileName = sha1(fabricCanvasJson);

      const payload = {
        json: fabricCanvasJson,
        renderImages: []
      };

      const areas = this.canvas.element.staticValues.area.filter((area) => area.perspective === designer.id);

      for (const area of areas) {
        const uploadResponse = await this.fabricCanvasService.uploadLayerImageForArea(designer.component.instance.fabricCanvas, area, designer.component.instance.renderImage, fileName, directory);

        if (!uploadResponse.message.error) {
          payload.renderImages.push({
            fileName: fileName + '-' + area.identifier,
            renderImageId: fileName + '-' + area.identifier,
            productId,
            directory,
            path: directory + fileName + '-' + area.identifier + '.png',
            extension: 'png',
            perspective: area.perspective,
            layer: area.layer,
            offsetX: area.left * 100 / designer.component.instance.renderImage.width,
            offsetY: area.top * 100 / designer.component.instance.renderImage.height
          });
        }
      }

      perspectivePayloads[designer.id] = payload;
    }
    this.store.dispatch(
      updateConfigurationState({
        updates: {
          set: [{
            sectionRepetition: 0,
            sectionId: this.canvas.element.sectionId,
            elementId: this.canvas.element.elementId,
            property: 'aptoElementDefinitionId',
            value: 'apto-element-image-upload',
          }, {
            sectionRepetition: 0,
            sectionId: this.canvas.element.sectionId,
            elementId: this.canvas.element.elementId,
            property: 'payload',
            value: perspectivePayloads,
          }],
        },
      })
    );

    this.actions$.pipe(
      ofType(getConfigurationStateSuccess),
      take(1)
    ).subscribe(() => {
      this.store.dispatch(
        setHideOnePage({ payload: false })
      );
    });
  }

  public reset(): void {
    // let dialogMessage = '';
    // this.resetMessage$.subscribe((next) => {
    //   dialogMessage = translate(next.content, this.locale);
    // });
    //
    // this.dialogService.openWarningDialog(DialogSizesEnum.md, 'Achtung!', dialogMessage, 'Abbrechen', 'Zurücksetzen' ).afterClosed().subscribe((next) => {
    //   if (true === next) {
    //     this.store.dispatch(
    //       updateConfigurationState({
    //         updates: {
    //           remove: [
    //             {
    //               // todo make designer compatiple with repeatable section logic (read correct repetition id)
    //               sectionRepetition: 0,
    //               sectionId: this.canvas.element.sectionId,
    //               elementId: this.canvas.element.elementId,
    //               property: null,
    //               value: null
    //             }
    //           ],
    //         },
    //       })
    //     );
    //
    //     this.store.dispatch(
    //       setHideOnePage({
    //         payload: false
    //       })
    //     );
    //   }
    // });
  }

  public cancel(): void {
    let dialogMessage = '';
    this.cancelMessage$.subscribe((next) => {
      dialogMessage = translate(next.content, this.locale);
    });

    this.dialogService.openWarningDialog(DialogSizesEnum.md, 'Achtung!', dialogMessage, 'Abbrechen', 'Verwerfen' ).afterClosed().subscribe((next) => {
      if (true === next) {
        this.store.dispatch(
          setHideOnePage({
            payload: false
          })
        );
      }
    });
  }

  private createDesigners(): void {
    this.designerContainer.clear();

    for (const perspective of this.perspectives) {
      const componentRef = this.designerContainer.createComponent(DesignerComponent);
      componentRef.instance.currentPerspective = perspective;
      componentRef.instance.visible = this.currentPerspective === perspective;
      componentRef.instance.locale = this.locale;
      componentRef.instance.canvas = this.canvas;
      componentRef.instance.product = this.product;
      this.designers.push({ id: perspective, component: componentRef });
    }
  }
}
