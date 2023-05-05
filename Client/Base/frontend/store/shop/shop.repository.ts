import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { environment } from '@apto-frontend/src/environments/environment';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { Language } from '@apto-base-frontend/store/language/language.model';
import { Connector, Shop } from '@apto-base-frontend/store/shop/shop.model';
import { CustomProperty } from "@apto-base-core/store/custom-property/custom-property.model";

@Injectable()
export class ShopRepository {
	public constructor(private messageBus: MessageBusService, private http: HttpClient) {}

	public getConnectorState(url: string): Observable<Connector> {
		return this.http
			.post(
				url,
				{
					data: {
						query: 'GetState',
						arguments: [],
					},
					encode: 'json',
				},
				{
					withCredentials: true,
				}
			)
			.pipe(map((response: any) => response.result));
	}

	public findShopContext(): Observable<{ shop: Shop; languages: Language[]; locale: string }> {
		return this.messageBus
			.query('FindShopContext', [window.location.host])
			.pipe(map((response) => this.responseToShop(response.result)));
	}

	private responseToShop(result: any): { shop: Shop; languages: Language[]; locale: string } {
		let locale = environment.defaultLocale;
		if (result.languages.length > 0) {
			locale = result.languages[0].isocode;
		}

		const languages: Language[] = [];
		result.languages.forEach((language: any) => {
			if (language.isocode === environment.defaultLocale) {
				locale = language.isocode;
			}

			languages.push({
				id: language.id,
				name: language.name,
				locale: language.isocode,
			});
		});

    let customProperties: CustomProperty[] = [];
    result.customProperties.forEach((customProperty: CustomProperty) => {
      customProperties.push({
        key: customProperty.key,
        value: customProperty.value,
        translatable: customProperty.translatable
      })
    });

		return {
			shop: {
				id: result.id,
				domain: result.domain,
				currency: result.currency,
				name: result.name,
				connectorUrl: result.connectorUrl,
				description: result.description,
        customProperties: customProperties
			},
			languages,
			locale,
		};
	}
}
