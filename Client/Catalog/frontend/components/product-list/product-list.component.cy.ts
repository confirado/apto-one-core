import { AptoSearchComponent } from '@apto-catalog-frontend/components/common/apto-search/apto-search.component';
import { ActionsSubject, StateObservable, Store, StoreModule } from '@ngrx/store';
import { NG_VALUE_ACCESSOR } from '@angular/forms';
import { ApplicationModule, forwardRef } from '@angular/core';
import { ProductListComponent } from '@apto-catalog-frontend/components/product-list/product-list.component';
import { mount } from 'cypress/angular';
import { TestBed } from '@angular/core/testing';
import { AptoFrontendModule } from '@apto-frontend/src/app/app.module';
import { AptoBackendModule } from '@apto-backend/src/app/app.module';

beforeEach(() => {

  // https://akhromieiev.com/error-no-provider-for-store-ng-test/
  TestBed.configureTestingModule({
    // declarations: [Component],
    // schemas: [CUSTOM_ELEMENTS_SCHEMA],
    imports: [StoreModule.forRoot({})]
  }).compileComponents();


  // https://timothycurchod.com/writings/testing-ngrx
  // TestBed.configureTestingModule({
  //   // imports: [RouterTestingModule],
  //   // declarations: [AppComponent, CounterComponent],
  //   providers: [Store]
  // }).compileComponents();

  // let store = TestBed.get(Store);
});

describe('component test', () => {
  it('mounts', () => {

    // cy.mount(ProductListComponent, {

    mount(ProductListComponent, {
      providers: [
        // {
        //   provide: NG_VALUE_ACCESSOR,
        //   useExisting: forwardRef(() => AptoSearchComponent),
        //   multi: true,
        // },
        Store
      ],
      componentProperties: {

      },
      imports: [AptoFrontendModule]
    });

    // cy.get('button').should('contains.text', 'Click me!')
  })
});
