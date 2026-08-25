import { firstValueFrom, of } from 'rxjs';

import { CatalogMessageBusService } from '@apto-catalog-frontend-service-catalog-message-bus';
import { ConfigurationRepository } from './configuration.repository';

type CatalogBusPort = Pick<CatalogMessageBusService, 'addBasketConfiguration' | 'fetchPartsList' | 'getConfigurationState'>;

describe('ConfigurationRepository', () => {
  it('forwards complete basket and parts-list arguments', async () => {
    const messageBus: jasmine.SpyObj<CatalogBusPort> = jasmine.createSpyObj<CatalogBusPort>('CatalogMessageBusService', ['addBasketConfiguration', 'fetchPartsList', 'getConfigurationState']);
    messageBus.addBasketConfiguration.and.returnValue(of({}));
    messageBus.fetchPartsList.and.returnValue(of([]));
    const repository = new ConfigurationRepository(messageBus as unknown as CatalogMessageBusService);
    const compressedState = [{ name: 'quantity', value: 2 }];

    await firstValueFrom(repository.addToBasket({ productId: 'product-1', compressedState, sessionCookies: 'session', locale: 'de_DE', quantity: 2, perspectives: ['front'], additionalData: { source: 'test' } }));
    await firstValueFrom(repository.fetchPartsList({ productId: 'product-1', compressedState, currency: 'EUR', customerGroupExternalId: 'group-1' }));

    expect(messageBus.addBasketConfiguration).toHaveBeenCalledWith('product-1', compressedState, 'session', 'de_DE', 2, ['front'], { source: 'test' });
    expect(messageBus.fetchPartsList).toHaveBeenCalledWith('product-1', compressedState, 'EUR', 'group-1');
  });

  it('maps section and element identities, state flags, values, and custom properties', async () => {
    const messageBus: jasmine.SpyObj<CatalogBusPort> = jasmine.createSpyObj<CatalogBusPort>('CatalogMessageBusService', ['addBasketConfiguration', 'fetchPartsList', 'getConfigurationState']);
    messageBus.getConfigurationState.and.returnValue(of({
      compressedState: [{ name: 'quantity', value: 2 }], failedRules: [{ id: 'soft-rule', softRule: true }], renderImages: { front: [] }, intention: { set: [] },
      configurationState: {
        sections: [{ id: 'section-1', identifier: 'main', allowMultiple: false, isMandatory: true, isHidden: false, repetition: 0, repeatableCalculatedValueName: null, repeatableType: 'Statisch', customProperties: [{ key: 'section' }], state: { active: true, disabled: false } }],
        elements: [{ id: 'element-1', identifier: 'width', sectionId: 'section-1', sectionRepetition: 0, isMandatory: true, customProperties: [{ key: 'element' }], state: { active: true, disabled: false, values: { width: 10 } } }],
      },
    }));
    const repository = new ConfigurationRepository(messageBus as unknown as CatalogMessageBusService);

    const result = await firstValueFrom(repository.getConfigurationState({ productId: 'product-1', compressedState: [], updates: { set: [] } }));

    expect(messageBus.getConfigurationState).toHaveBeenCalledWith('product-1', [], { set: [] });
    expect(result.state.sections[0]).toEqual(jasmine.objectContaining({ id: 'section-1', identifier: 'main', active: true, disabled: false, mandatory: true, hidden: false }));
    expect(result.state.elements[0]).toEqual(jasmine.objectContaining({ id: 'element-1', identifier: 'width', sectionId: 'section-1', sectionIdentifier: 'width', sectionRepetition: 0, values: { width: 10 } }));
  });
});
