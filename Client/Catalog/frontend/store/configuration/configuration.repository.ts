import { Injectable } from '@angular/core';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import {
	AddBasketConfigurationArguments,
	AddGuestConfigurationArguments,
	ComputedValues,
	Configuration,
	RenderImage,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import { map, Observable } from 'rxjs';
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

	private responseToConfigurationState(result: any): { state: Configuration | null, renderImages: [] } {
		const state: Configuration = {
			compressedState: result.compressedState || [],
			sections: [],
			elements: []
		};
		const responseState = result.configurationState;

		for (const sectionId in responseState) {
			if (!Object.prototype.hasOwnProperty.call(responseState, sectionId)) {
				// eslint-disable-next-line no-continue
				continue;
			}
			const section = responseState[sectionId];

			// create section state
			state.sections.push({
				id: sectionId,
				identifier: section.identifier,
				active: section.state.active,
				disabled: section.state.disabled,
				multiple: section.allowMultiple,
				mandatory: section.isMandatory,
				hidden: section.isHidden,
			});

			for (const elementId in section.elements) {
				if (!Object.prototype.hasOwnProperty.call(section.elements, elementId)) {
					// eslint-disable-next-line no-continue
					continue;
				}
				const element = section.elements[elementId];

				// create element state
				state.elements.push({
					id: elementId,
					identifier: element.identifier,
					sectionId,
					sectionIdentifier: section.identifier,
					active: element.state.active,
					disabled: element.state.disabled,
					mandatory: element.isMandatory,
					values: element.state.values,
          attachments: element.attachments,
				});
			}
		}

		return { state: state, renderImages: result.renderImages };
	}
}
