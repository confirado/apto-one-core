import { Injectable } from '@angular/core';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { Element, Group, Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { map, Observable } from 'rxjs';

@Injectable()
export class ProductRepository {
	private mediaUrl = environment.api.media + '/';

	public constructor(private messageBus: MessageBusService) {}

	public findProductsByFilter(action: any): Observable<Product[]> {
    const args = action.payload || {};
		return this.messageBus.query('FindProductsByFilter', [args]).pipe(map((response) => this.responseToProducts(response)));
	}

	public findConfigurableProduct(
		id: string,
    type: string | null
	): Observable<{ product: Product; groups: Group[]; sections: Section[]; elements: Element<unknown>[]; configuration: any; }> {
		return this.messageBus
			.query('FindProductConfiguration', [id, type])
			.pipe(map((response) => this.responseToConfigurableProduct(response.result)));
	}

	private responseToProduct(response: any): Product {
		let { previewImage } = response;
		if (!response.previewImage) {
			previewImage = null;
		}

		if (response.previewImageMediaFile) {
			previewImage = this.mediaFileToMediaPath(response.previewImageMediaFile);
		}

		return {
			id: response.id,
			identifier: response.identifier,
			seoUrl: response.seoUrl,
			name: response.name,
			description: response.description,
			previewImage: previewImage ? this.mediaUrl + previewImage : null,
			useStepByStep: response.useStepByStep,
      keepSectionOrder: response.keepSectionOrder,
			position: response.position,
			customProperties: response.customProperties,
      hidden: response.hidden,
      active: response.active,
      minPurchase: response.minPurchase,
      maxPurchase: response.maxPurchase,
      metaDescription: response.metaDescription,
      metaTitle: response.metaTitle,
		};
	}

	private responseToProducts(response: any): Product[] {
		const products: Product[] = [];
		response.result.data.forEach((product: any) => {
			products.push(this.responseToProduct(product));
		});

		return products;
	}

	private responseToConfigurableProduct(response: any): {
		product: Product;
		groups: Group[];
		sections: Section[];
		elements: Element<unknown>[];
    configuration: any;
	} {
    const product = response.product;
    const configuration = response.configuration;
		const groups: Group[] = [];
		const sections: Section[] = [];
		const elements: Element<unknown>[] = [];
		const addedGroups: string[] = [];

    product.sections.forEach((section: any) => {
			// map group
			let group: Group = section.group.length > 0 ? section.group[0] : null;
			if (group !== null) {
				group = {
					id: group.id,
					identifier: group.identifier,
					name: group.name,
					position: group.position,
				};

				if (!addedGroups.includes(group.id)) {
					groups.push(group);
					addedGroups.push(group.id);
				}
			}

      // get element preview image
      let previewImage = null;
      if (section.previewImage) {
        previewImage = this.mediaFileToMediaPath(section.previewImage.mediaFile);
      }
		// map section
		sections.push({
			id: section.id,
			identifier: section.identifier,
			groupId: group ? group.id : null,
			groupIdentifier: group ? group.identifier : null,
			name: section.name,
			description: section.description,
			allowMultiple: section.allowMultiple,
			isHidden: section.isHidden,
			isMandatory: section.isMandatory,
			position: section.position,
			previewImage: previewImage ? this.mediaUrl + previewImage.substring(1) : null,
			isZoomable: section.isZoomable,
			repeatableType: section.repeatableType,
			repeatableCalculatedValueName: section.repeatableCalculatedValueName
		});

			section.elements.forEach((element: any) => {
				// get element preview image
				let previewImage = null;
				if (element.previewImage) {
					previewImage = this.mediaFileToMediaPath(element.previewImage.mediaFile);
				}

				// map element
				elements.push({
					id: element.id,
					identifier: element.identifier,
					sectionId: section.id,
					sectionIdentifier: section.identifier,
					name: element.name,
					description: element.description,
					definition: element.definition,
					errorMessage: element.errorMessage,
					previewImage: previewImage ? this.mediaUrl + previewImage : null,
					isMandatory: element.isMandatory,
					position: element.position,
          attachments: element.attachments,
				  zoomFunction: element.zoomFunction,
				  sectionRepetition: 0,
          gallery: element.gallery
				});
			});
		});

		return {
			product: this.responseToProduct(product),
			groups,
			sections,
			elements,
      configuration
		};
	}

	public mediaFileToMediaPath(mediaFile: any): string | null {
		if (!mediaFile.filename) {
			return null;
		}

		return `${mediaFile.path}/${mediaFile.filename}.${mediaFile.extension}`;
	}
}
