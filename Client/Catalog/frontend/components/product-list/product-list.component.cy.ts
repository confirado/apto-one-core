import { AptoSearchComponent } from '@apto-catalog-frontend/components/common/apto-search/apto-search.component';
import { ActionsSubject, StateObservable, Store } from '@ngrx/store';
import { NG_VALUE_ACCESSOR } from '@angular/forms';
import { forwardRef } from '@angular/core';
import { ProductListComponent } from '@apto-catalog-frontend/components/product-list/product-list.component';

describe('component test', () => {
  it('mounts', () => {

    cy.mount(ProductListComponent, {
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
    });

    cy.get('button').should('contains.text', 'Click me!')
  })
});
