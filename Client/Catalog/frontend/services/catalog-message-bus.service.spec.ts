import { firstValueFrom, of } from 'rxjs';

import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { onError, resetLoadingFlagAction } from '@apto-catalog-frontend-configuration-actions';
import { CatalogMessageBusService } from './catalog-message-bus.service';

describe('CatalogMessageBusService', () => {
  const success = <T>(result: T): MessageBusResponse<T> => ({
    result,
    message: { messageName: 'FindPerspectivesByState', message: '', date: '', duration: 0, error: false, errorType: null as never, errorPayload: null, uuid: '', url: '' },
  } as unknown as MessageBusResponse<T>);

  it('returns successful query results without dispatching an error action', async () => {
    const messageBus = jasmine.createSpyObj<MessageBusService>('MessageBusService', ['query', 'command']);
    const store = jasmine.createSpyObj('Store', ['dispatch']);
    messageBus.query.and.returnValue(of(success(['front'])));
    const service = new CatalogMessageBusService(messageBus, store);

    await expectAsync(firstValueFrom(service.getPerspectives([], 'product-1'))).toBeResolvedTo(['front']);
    expect(messageBus.query).toHaveBeenCalledWith('FindPerspectivesByState', [[], 'product-1']);
    expect(store.dispatch).not.toHaveBeenCalled();
  });

  it('filters a message-bus error and resets the loading state', async () => {
    const message = { ...success(null).message, error: true, errorType: 'InvalidConfigurationStateChangeException' as never, errorPayload: { rule: 'width' } };
    const messageBus = jasmine.createSpyObj<MessageBusService>('MessageBusService', ['query', 'command']);
    const store = jasmine.createSpyObj('Store', ['dispatch']);
    messageBus.query.and.returnValue(of({ result: null, message } as unknown as MessageBusResponse<unknown>));
    const service = new CatalogMessageBusService(messageBus, store);

    await expectAsync(firstValueFrom(service.getPerspectives([], 'product-1'))).toBeRejected();
    expect(store.dispatch).toHaveBeenCalledWith(onError({ message: message as never }));
    expect(store.dispatch).toHaveBeenCalledWith(resetLoadingFlagAction());
  });
});
