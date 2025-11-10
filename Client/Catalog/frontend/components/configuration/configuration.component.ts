import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { initConfiguration } from '@apto-catalog-frontend-configuration-actions';
import { selectConfiguration } from '@apto-catalog-frontend-configuration-selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Title } from '@angular/platform-browser';
import { Meta } from '@angular/platform-browser';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { Subject, takeUntil } from 'rxjs';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';

@Component({
	selector: 'apto-configuration',
	templateUrl: './configuration.component.html',
	styleUrls: ['./configuration.component.scss'],
})
export class ConfigurationComponent implements OnInit, OnDestroy {
	public readonly product$ = this.store.select(selectProduct);
	public readonly configuration$ = this.store.select(selectConfiguration);
  private readonly destroy$ = new Subject<void>();
  private locale$ = this.store.select(selectLocale);
  private locale: string = environment.defaultLocale;

	public constructor(
    private route: ActivatedRoute,
    private store: Store,
    private titleService: Title,
    private metaService: Meta
  ) {}

	public ngOnInit(): void {

    this.locale = environment.defaultLocale;
    this.locale$.pipe(
      takeUntil(this.destroy$)
    ).subscribe(locale => {
      this.locale = locale;
    });

    this.product$.pipe(
      takeUntil(this.destroy$)
    ).subscribe((data: Product) => {

      if (data && data.metaTitle[this.locale]) {
        this.titleService.setTitle(data.metaTitle[this.locale]);
      }

      if (data && data.metaDescription[this.locale]) {
        this.metaService.updateTag({ name: 'description', content: data.metaDescription[this.locale] });
      }
    });

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
    else if (configurationId && configurationType) {
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

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
