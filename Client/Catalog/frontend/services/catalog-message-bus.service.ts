/* eslint-disable no-shadow */
import { Injectable } from '@angular/core';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import {
	MaterialPickerColor,
	MaterialPickerFilter,
	MaterialPickerItem,
	MaterialPickerPriceGroup,
	PropertyGroup,
} from '@apto-catalog-frontend/models/material-picker';
import { Page } from '@apto-catalog-frontend/models/pagination';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { onError, resetLoadingFlagAction } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { CompressedState, ComputedValues, HumanReadableFullStatePayload, HumanReadableState, PartsListPart, RenderImage, StatePrice } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { filter, map, Observable } from 'rxjs';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';

@Injectable({
	providedIn: 'root',
})
export class CatalogMessageBusService {
	public constructor(private messageBusService: MessageBusService, private store: Store) {}

	private query<Result>(query: string, args: any[]): Observable<Result> {
		return this.messageBusService.query<Result>(query, args).pipe(
			filter((response) => {
				if (response.message.error) {
					this.store.dispatch(onError({ message: response.message }));
          // we close loading from here because in case of error it is not done in getconfiguration action
          this.store.dispatch(resetLoadingFlagAction());
				}
				return !response.message.error;
			}),
			map((response) => response.result)
		);
	}

	private command<Result>(command: string, args: any[]): Observable<Result> {
		return this.messageBusService.command<Result>(command, args).pipe(
			filter((response) => {
				if (response.message.error) {
					this.store.dispatch(onError({ message: response.message }));
          // we close loading from here because in case of error it is not done in getconfiguration action
          this.store.dispatch(resetLoadingFlagAction());
				}
				return !response.message.error;
			}),
			map((response) => response.result)
		);
	}

	public findRenderImageByState(compressedState: any, perspective: string, productId: string): Observable<RenderImage[] | undefined> {
		return this.query<RenderImage[] | undefined>('FindRenderImageByState', [compressedState, perspective, productId]);
	}

	public findRenderImagesByState(compressedState: any, perspectives: string[], productId: string): Observable<RenderImage[] | undefined> {
		return this.query<RenderImage[] | undefined>('FindRenderImagesByState', [compressedState, perspectives, productId]);
	}

	public findPriceByState(productId: string, compressedState: any, connector: SelectConnector, currentUser: FrontendUser | null): Observable<StatePrice> {
    let customerGroupId = connector.customerGroup.id;
    if (currentUser !== null && connector.configured === false) {
      customerGroupId = currentUser.customerGroup.externalId
    }

		return this.query('FindPriceByState', [
			productId,
			compressedState,
			connector.shopCurrency,
			connector.displayCurrency,
      customerGroupId,
			connector.locale,
			connector.sessionCookies,
			connector.taxState,
      connector.user
		]);
	}

	public getPerspectives(compressedState: any, productId: string): Observable<string[]> {
		return this.query('FindPerspectivesByState', [compressedState, productId]);
	}

	public findProductComputedValuesCalculated(productId: string, compressedState: any): Observable<ComputedValues> {
		return this.query<ComputedValues>('FindProductComputedValuesCalculated', [productId, compressedState]);
	}

	public findSelectBoxItems(elementId: string): Observable<Page<SelectItem>> {
		return this.query<Page<SelectItem>>('FindSelectBoxItems', [elementId]);
	}

	public getConfigurationState(productId: string, compressedState: any, updates: any): Observable<unknown> {
		return this.query('GetConfigurationState', [productId, compressedState, updates]);
	}

	public findHumanReadableState(productId: string, compressedState: any): Observable<any> {
		return this.query('FindHumanReadableState', [productId, compressedState]);
	}

	public findMaterialPickerPoolItemsFiltered(poolId: string, filter: MaterialPickerFilter): Observable<Page<MaterialPickerItem>> {
		return this.query('FindMaterialPickerPoolItemsFiltered', [poolId, filter]);
	}

	public findMaterialPickerPoolPriceGroups(poolId: string): Observable<MaterialPickerPriceGroup[]> {
		return this.query('FindMaterialPickerPoolPriceGroups', [poolId]);
	}

	public findMaterialPickerPoolPropertyGroups(poolId: string): Observable<PropertyGroup[]> {
		return this.query('FindMaterialPickerPoolPropertyGroups', [poolId]);
	}

	public findMaterialPickerPoolColors(poolId: string, filter: MaterialPickerFilter): Observable<{ [key: string]: MaterialPickerColor }> {
		return this.query('FindMaterialPickerPoolColors', [poolId, filter]);
	}

  public findElementComputableValues(compressedState: CompressedState[], sectionId: string, elementId: string, repetition: number): Observable<any> {
    return this.query('FindElementComputableValues', [compressedState, sectionId, elementId, repetition]);
  }

	public incrementMaterialPickerMaterialClicks(materialId: string): Observable<void> {
		return this.command('IncrementMaterialPickerMaterialClicks', [materialId]);
	}

	public addBasketConfiguration(
		productId: string,
		compressedState: any,
		sessionCookies: any,
		locale: string | undefined,
		quantity: number,
		perspectives: unknown,
		additionalData: any
	): Observable<unknown> {
		return this.command<unknown>('AddBasketConfiguration', [
			productId,
			compressedState,
			sessionCookies,
			locale,
			quantity,
			perspectives,
			additionalData,
		]);
	}

	public updateBasketConfiguration(
		productId: string,
		configurationId: string,
		compressedState: any,
		sessionCookies: any,
		locale: string | undefined,
		quantity: number,
		perspectives: unknown,
		additionalData: any
	): Observable<unknown> {
		return this.command<unknown>('UpdateBasketConfiguration', [
			productId,
      configurationId,
			compressedState,
			sessionCookies,
			locale,
			quantity,
			perspectives,
			additionalData,
		]);
	}

	public addGuestConfiguration(
		productId: string,
		compressedState: any,
		email: string,
		name: string,
		sendMail: boolean,
		id: string,
		payload: any[]
	): Observable<void> {
		return this.command('AddGuestConfiguration', [productId, compressedState, email, name, sendMail, id, payload]);
	}

  public fetchPartsList(
    productId: string,
    compressedState: any,
    currency: string,
    customerGroupExternalId: string,
    categoryId?: string
  ): Observable<PartsListPart[]> {
    return this.query('AptoPartsListFindPartsList', [productId, compressedState, currency, customerGroupExternalId, categoryId]);
  }

  public fetchPartsListForCategory(
    productId: string,
    compressedState: any,
    currency: string,
    customerGroupExternalId: string,
    categoryId: string
  ): Observable<PartsListPart[]> {
    return this.query('AptoPartsListFindPartsListForCategory', [productId, compressedState, currency, customerGroupExternalId, categoryId]);
  }

  public addOfferConfiguration(
    productId: string,
    compressedState: CompressedState[],
    email: string,
    name: string,
    payload: HumanReadableFullStatePayload | undefined[]
  ): Observable<void> {
    if (!name) {
      name = '';
    }

    if (!payload) {
      payload = [];
    }

    return this.command('AddOfferConfiguration', [productId, compressedState, email, name, payload]);
  }
}
