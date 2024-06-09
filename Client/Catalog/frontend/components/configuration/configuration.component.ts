import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { initConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Title } from '@angular/platform-browser';
import { Meta } from '@angular/platform-browser';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { Subject, takeUntil } from 'rxjs';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

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
  public readonly contentSnippetMetaTitle$ = this.store.select(selectContentSnippet('AptoProductPage.defaultPageMetaTitle'));
  public readonly contentSnippetMetaDescription$ = this.store.select(selectContentSnippet('AptoProductPage.defaultPageMetaDescription'));
  private defaultMetaTitle = '';
  private defaultMetaDescription = '';

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

    this.contentSnippetMetaTitle$.pipe(
      takeUntil(this.destroy$)
    ).subscribe(data => {
      this.defaultMetaTitle = data.content[this.locale];
    });

    this.contentSnippetMetaDescription$.pipe(
      takeUntil(this.destroy$)
    ).subscribe(data => {
      this.defaultMetaDescription = data.content[this.locale];
    });

    this.product$.pipe(
      takeUntil(this.destroy$)
    ).subscribe((data: Product) => {

      if (data && data.metaTitle[this.locale]) {
        this.titleService.setTitle(data.metaTitle[this.locale]);
      } else {
        this.titleService.setTitle(this.defaultMetaTitle);
      }

      if (data && data.metaDescription[this.locale]) {
        this.metaService.updateTag({ name: 'description', content: data.metaDescription[this.locale] });
      } else {
        this.metaService.updateTag({ name: 'description', content: this.defaultMetaDescription[this.locale] });
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

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
