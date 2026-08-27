import { Action } from '@ngrx/store';
import { firstValueFrom, Observable, of, Subject, take } from 'rxjs';

import { selectCurrentUser } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { CatalogMessageBusService } from '@apto-catalog-frontend-service-catalog-message-bus';
import { ConfigurationRepository } from './configuration.repository';
import { ConfigurationEffects } from './configuration.effects';
import {
  addToBasket, addToBasketSuccess, fetchPartsList, fetchPartsListSuccess, getConfigurationState,
  getConfigurationStateSuccess, setPrevStepSuccess, updateConfigurationState,
} from './configuration.actions';
import { selectConfiguration, selectProduct, selectProgressState } from './configuration.selectors';
import { selectRuleRepairSettings } from '@apto-catalog-frontend/store/product/product.selectors';

type SelectorValues = Map<object, unknown>;
type StorePort = { select(selector: object): Observable<unknown> };
type ConfigurationRepositoryPort = Pick<ConfigurationRepository, 'getConfigurationState' | 'getComputedValues' | 'getPerspectives' | 'getStatePrice' | 'addToBasket' | 'updateBasket' | 'fetchPartsList'>;

function createHarness(values: SelectorValues) {
  const actions$ = new Subject<Action>();
  const repository = jasmine.createSpyObj<ConfigurationRepositoryPort>('ConfigurationRepository', ['getConfigurationState', 'getComputedValues', 'getPerspectives', 'getStatePrice', 'addToBasket', 'updateBasket', 'fetchPartsList']);
  repository.getConfigurationState.and.returnValue(of(null));
  repository.getComputedValues.and.returnValue(of({}));
  repository.getPerspectives.and.returnValue(of([]));
  repository.getStatePrice.and.returnValue(of(null));
  repository.addToBasket.and.returnValue(of({}));
  repository.updateBasket.and.returnValue(of({}));
  repository.fetchPartsList.and.returnValue(of([]));
  const store: StorePort = { select: (selector) => of(values.get(selector)) };
  const effects = new ConfigurationEffects(
    actions$ as never, repository as unknown as ConfigurationRepository, jasmine.createSpyObj('ProductRepository', ['findConfigurableProduct']), store as never,
    jasmine.createSpyObj<CatalogMessageBusService>('CatalogMessageBusService', ['findHumanReadableState', 'findElementComputableValues']),
    jasmine.createSpyObj('MatSnackBar', ['open']), jasmine.createSpyObj('DialogService', ['openCustomDialog']),
  );
  return { actions$, effects, repository };
}

describe('ConfigurationEffects', () => {
  const configuration = { productId: 'product-1', currentPerspective: 'front', perspectives: ['front'], quantity: 2, state: { compressedState: [], sections: [], elements: [], failedRules: [] } };
  const connector = { locale: 'de_DE', sessionCookies: [], shopCurrency: { currency: 'EUR' }, displayCurrency: { currency: 'EUR' }, customerGroup: { id: 'group-1' }, configured: true };

  it('turns an element update into one state request with current store context', async () => {
    const { actions$, effects } = createHarness(new Map<object, unknown>([[selectConfiguration, configuration], [selectCurrentUser, null], [selectConnector, connector]]));
    const result = firstValueFrom(effects.updateConfiguration$.pipe(take(1)));
    actions$.next(updateConfigurationState({ updates: { set: [] } }));

    expect(await result).toEqual(getConfigurationState({ payload: { productId: 'product-1', compressedState: [], connector, updates: { set: [] }, currentPerspective: 'front', currentUser: null } }));
  });

  it('adds configured rule repair and emits a complete refreshed state', async () => {
    const { actions$, effects, repository } = createHarness(new Map<object, unknown>([[selectRuleRepairSettings, { strategy: 'repair' }]]));
    repository.getConfigurationState.and.returnValue(of({ state: { compressedState: [], sections: [], elements: [], failedRules: [] }, renderImages: [], updates: { set: [] } }));
    repository.getComputedValues.and.returnValue(of({ width: '10' }));
    repository.getPerspectives.and.returnValue(of(['front']));
    repository.getStatePrice.and.returnValue(of(null));
    const result = firstValueFrom(effects.getConfigurationState$.pipe(take(1)));
    const request = getConfigurationState({ payload: { productId: 'product-1', compressedState: [], connector, updates: { set: [] }, currentPerspective: null, currentUser: null } });
    actions$.next(request as never);

    expect(await result).toEqual(getConfigurationStateSuccess({ payload: { productId: 'product-1', configuration: { compressedState: [], sections: [], elements: [], failedRules: [] }, renderImages: [], computedValues: { width: '10' }, perspectives: ['front'], currentPerspective: 'front', statePrice: null, updates: { set: [] } } }));
    expect(repository.getConfigurationState).toHaveBeenCalledWith(jasmine.objectContaining({ updates: { set: [], repair: { strategy: 'repair' } } }));
  });

  it('removes active values in following sections when navigating backwards in ordered products', async () => {
    const nextSection = { id: 'next', repetition: 0, active: true, disabled: false };
    const { actions$, effects } = createHarness(new Map<object, unknown>([[selectConfiguration, { ...configuration, state: { ...configuration.state, sections: [nextSection], elements: [{ id: 'element-2', sectionId: 'next', sectionRepetition: 0, active: true }] } }], [selectProgressState, { afterSteps: [{ section: nextSection }] }], [selectProduct, { product: { keepSectionOrder: true } }]]));
    const result = firstValueFrom(effects.resetSteps$.pipe(take(1)));
    actions$.next(setPrevStepSuccess() as never);

    expect(await result).toEqual(updateConfigurationState({ updates: { remove: [{ sectionId: 'next', elementId: 'element-2', sectionRepetition: 0, property: '', value: '' }] } }));
  });

  it('sends request-form basket data and emits the basket success action first', async () => {
    const { actions$, effects, repository } = createHarness(new Map<object, unknown>([[selectConfiguration, configuration], [selectCurrentUser, null], [selectConnector, connector]]));
    const result = firstValueFrom(effects.addToBasket$.pipe(take(1)));
    actions$.next(addToBasket({ payload: { type: 'REQUEST_FORM', formData: { email: 'ada@example.test' }, humanReadableState: [] as never } }));

    expect(await result).toEqual(addToBasketSuccess());
    expect(repository.addToBasket).toHaveBeenCalledWith(jasmine.objectContaining({ productId: 'product-1', quantity: 2, perspectives: ['front'], additionalData: jasmine.objectContaining({ productId: 'product-1', humanReadableState: [], customerGroupExternalId: 'group-1' }) }));
  });

  it('requests and stores the parts list with the current connector context', async () => {
    const { actions$, effects, repository } = createHarness(new Map<object, unknown>([[selectConfiguration, configuration], [selectConnector, connector]]));
    repository.fetchPartsList.and.returnValue(of([]));
    const result = firstValueFrom(effects.fetchPartsList$.pipe(take(1)));
    actions$.next(fetchPartsList() as never);

    expect(await result).toEqual(fetchPartsListSuccess({ payload: [] }));
    expect(repository.fetchPartsList).toHaveBeenCalledWith({ productId: 'product-1', compressedState: [], currency: 'EUR', customerGroupExternalId: 'group-1' });
  });
});
