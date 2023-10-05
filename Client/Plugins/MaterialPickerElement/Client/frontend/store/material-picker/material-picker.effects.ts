import { Injectable } from "@angular/core";
import { map, switchMap } from "rxjs/operators";
import { forkJoin } from "rxjs";
import { Store } from "@ngrx/store";
import { Actions, createEffect, ofType } from "@ngrx/effects";
import { CatalogMessageBusService } from "@apto-catalog-frontend/services/catalog-message-bus.service";
import {
  findPoolItems,
  findPoolItemsSuccess,
  initMaterialPicker,
  initMaterialPickerSuccess
} from "@apto-material-picker-element-frontend/store/material-picker/material-picker.actions";

@Injectable()
export class MaterialPickerEffects {
  public constructor(private store$: Store, private actions$: Actions, private catalogMessageBusService: CatalogMessageBusService) {
  }

  public initMaterialPicker$ = createEffect(() =>
    this.actions$.pipe(
      ofType(initMaterialPicker),
      switchMap(action => forkJoin([
        this.catalogMessageBusService.findMaterialPickerPoolItemsFiltered(
          action.payload.poolId,
          action.payload.filter,
        ),
        this.catalogMessageBusService.findMaterialPickerPoolColors(
          action.payload.poolId,
          action.payload.filter,
        ),
        this.catalogMessageBusService.findMaterialPickerPoolPriceGroups(
          action.payload.poolId
        ),
        this.catalogMessageBusService.findMaterialPickerPoolPropertyGroups(
          action.payload.poolId
        )
      ]).pipe(
        map((result) => {
          return initMaterialPickerSuccess({
            payload: {
              items: result[0].data,
              colors: Object.values(result[1]),
              priceGroups: result[2],
              propertyGroups: result[3]
            }
          });
        })
      ))
    )
  );

  public findPoolItems$ = createEffect(() =>
    this.actions$.pipe(
      ofType(findPoolItems),
      switchMap(action => forkJoin([
        this.catalogMessageBusService.findMaterialPickerPoolItemsFiltered(
          action.payload.poolId,
          action.payload.filter,
        ),
        this.catalogMessageBusService.findMaterialPickerPoolColors(
          action.payload.poolId,
          action.payload.filter,
        )
      ]).pipe(
        map((result) => {
          return findPoolItemsSuccess({
            payload: {
              items: result[0].data,
              colors: Object.values(result[1])
            }
          });
        })
      ))
    )
  );
}
