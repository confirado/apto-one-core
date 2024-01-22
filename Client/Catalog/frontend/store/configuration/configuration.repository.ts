import { Injectable } from '@angular/core';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import {
  AddBasketConfigurationArguments,
  AddGuestConfigurationArguments, CompressedState,
  ComputedValues,
  Configuration, FetchPartsListArguments, GetParameterStateArguments, PartsListPart,
  RenderImage,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import { map, Observable, tap } from 'rxjs';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';

@Injectable()
export class ConfigurationRepository {
	public constructor(private catalogMessageBusService: CatalogMessageBusService) {}

	public getStatePrice(productId: string, compressedState: any, connector: SelectConnector, currentUser: FrontendUser | null): Observable<string> {
		return this.catalogMessageBusService.findPriceByState(productId, compressedState, connector, currentUser);
	}

	public getRenderImages(productId: string, perspectives: string[], compressedState: any): Observable<RenderImage[] | undefined> {
		return this.catalogMessageBusService.findRenderImagesByState(compressedState, perspectives, productId);
	}

	public getPerspectives(productId: string, compressedState: any): Observable<string[]> {
		return this.catalogMessageBusService.getPerspectives(compressedState, productId);
	}

	public getRenderImage(productId: string, perspective: string, compressedState: any): Observable<string> {
		return this.catalogMessageBusService
			.findRenderImageByState(compressedState, perspective, productId)
			.pipe(map((result) => `${window.location.protocol}//${window.location.hostname}${result}`));
	}

	public getComputedValues(productId: string, compressedState: any): Observable<ComputedValues> {
		return this.catalogMessageBusService.findProductComputedValuesCalculated(productId, compressedState);
	}

	public getConfigurationState(params: any): Observable<{state: Configuration | null, renderImages: []}> {
		const args = [params.productId, params.compressedState, params.updates];

		return this.catalogMessageBusService
			.getConfigurationState(params.productId, params.compressedState, params.updates)
			.pipe(map((response) => this.responseToConfigurationState(response)));
	}

	public getParameterState(params: GetParameterStateArguments): Observable<{ state: CompressedState[] | null }> {
		const args = [params.compressedState, params.parameters];

		return this.catalogMessageBusService.getParameterState(params.compressedState, params.parameters)
			.pipe(
        tap(console.log),
        map((response) => this.responseToParameterState(response))
      );
	}


	public addToBasket(params: AddBasketConfigurationArguments): Observable<unknown> {
		return this.catalogMessageBusService.addBasketConfiguration(
			params.productId,
			params.compressedState,
			params.sessionCookies,
			params.locale,
			params.quantity,
			params.perspectives,
			params.additionalData
		);
	}

	public addGuestConfiguration(params: AddGuestConfigurationArguments): Observable<unknown> {
		return this.catalogMessageBusService.addGuestConfiguration(
			params.productId,
			params.compressedState,
			params.email,
			params.name,
			params.sendMail,
			params.id,
			params.payload
		);
	}

  public fetchPartsList(params: FetchPartsListArguments): Observable<PartsListPart[]> {
    return this.catalogMessageBusService.fetchPartsList(
      params.productId,
      params.compressedState,
      params.currency,
      params.customerGroupExternalId,
    );
  }

	private responseToConfigurationState(result: any): { state: Configuration | null, renderImages: [] } {
		const state: Configuration = {
			compressedState: result.compressedState || [],
			sections: [],
			elements: [],
      failedRules: result.failedRules || [],
		};

		const responseState = result.configurationState;

    for (const section of responseState.sections) {
      state.sections.push({
        id: section.id,
        identifier: section.identifier,
        active: section.state.active,
        disabled: section.state.disabled,
        multiple: section.allowMultiple,
        mandatory: section.isMandatory,
        hidden: section.isHidden,
        repetition: section.repetition,
        repeatableCalculatedValueName: section.repeatableCalculatedValueName,
        repeatableType: section.repeatableType,
      });
    }

    for (const element of responseState.elements) {
      state.elements.push({
        id: element.id,
        identifier: element.identifier,
        sectionId: element.sectionId,
        sectionRepetition: element.sectionRepetition,
        sectionIdentifier: element.identifier,
        active: element.state.active,
        disabled: element.state.disabled,
        mandatory: element.isMandatory,
        values: element.state.values,
        attachments: element.attachments,
      });
    }

		return { state: state, renderImages: result.renderImages };
	}

  private responseToParameterState(result: CompressedState[]): { state: CompressedState[] | null } {
    return { state: result };
  }
}
