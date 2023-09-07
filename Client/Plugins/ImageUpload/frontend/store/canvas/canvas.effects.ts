import { Injectable } from '@angular/core';
import { Store } from '@ngrx/store';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { map, switchMap, withLatestFrom } from 'rxjs/operators';
import { selectConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { getRenderImagesSuccess } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { CanvasRepository } from '@apto-image-upload-frontend/store/canvas/canvas.repository';
import { findEditableRenderImage } from '@apto-image-upload-frontend/store/canvas/canvas.actions';

@Injectable()
export class CanvasEffects {
  public constructor(private store$: Store, private actions$: Actions, private canvasRepository: CanvasRepository) {}

  public findEditableRenderImage$ = createEffect(() =>
    this.actions$.pipe(
      ofType(findEditableRenderImage),
      withLatestFrom(this.store$.select(selectConfiguration)),
      switchMap(([action, configuration]) => this.canvasRepository.findEditableRenderImage(
        configuration.state.compressedState,
        configuration.productId,
        action.payload.perspective,
        action.payload.renderImageIds
      )),
      map((renderImages) => getRenderImagesSuccess({ payload: {renderImages: renderImages} }))
    )
  );
}
