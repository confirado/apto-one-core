import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { initConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-configuration',
	templateUrl: './configuration.component.html',
	styleUrls: ['./configuration.component.scss'],
})
export class ConfigurationComponent implements OnInit {
	public readonly product$ = this.store.select(selectProduct);
	public readonly configuration$ = this.store.select(selectConfiguration);

	public constructor(private route: ActivatedRoute, private store: Store) {}

	public ngOnInit(): void {
		const productId = this.route.snapshot.paramMap.get('productId');
    const configurationId = this.route.snapshot.paramMap.get('configurationId');
    const configurationType = this.route.snapshot.paramMap.get('configurationType');

		if (productId) {
			this.store.dispatch(
				initConfiguration({
					payload: {
						id: productId,
            type: null
					},
				})
			);
		}

    if (configurationId && configurationType) {
      this.store.dispatch(
        initConfiguration({
          payload: {
            id: configurationId,
            type: configurationType
          },
        })
      );
    }
	}
}
