import { mount } from 'cypress/angular';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { provideMockStore } from '@ngrx/store/testing';
import { of } from 'rxjs';

import { AptoMaterialPickerElementFrontendModule } from '@apto-material-picker-element-frontend/apto-material-picker-element-frontend.module';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { MaterialPickerHoverComponent } from './material-picker-hover.component';
import { buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';

const poolItem: any = {
  material: {
    id: 'oak', previewImage: { path: 'oak.png' }, reflection: null, transmission: null, absorption: null,
  },
  priceGroup: { additionalCharge: 0 },
};

describe('MaterialPickerHoverComponent', () => {
  it('opens the details dialog with the active element and pool item', () => {
    const dialogRef = { componentInstance: {} } as any;
    const dialogService = { openCustomDialog: cy.stub().returns(dialogRef) };

    mount(MaterialPickerHoverComponent, {
      componentProperties: { element: { state: {}, element: {} } as any, poolItem },
      imports: [AptoMaterialPickerElementFrontendModule, StoreModule.forRoot({}), EffectsModule.forRoot([])],
      providers: [
        provideMockStore({ initialState: buildMockStoreInitialState({
          aptoBase: { contentSnippets: { snippets: [{ name: 'plugins', content: {}, children: [{ name: 'materialPickerElement', content: {}, children: [{ name: 'poolItemOpenDetails', content: { de_DE: 'Details' } }] }] }] } },
        }) }),
        { provide: DialogService, useValue: dialogService },
      ],
    });

    cy.get('[data-cy="material-open-details"]').click();
    cy.wrap(dialogService.openCustomDialog).should('have.been.calledOnce');
    cy.then(() => expect(dialogRef.componentInstance.data).to.deep.equal({ element: { state: {}, element: {} }, poolItem }));
  });
});
