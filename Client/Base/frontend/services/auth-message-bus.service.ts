import { Injectable } from '@angular/core';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { Store } from '@ngrx/store';
import { filter, map, Observable } from 'rxjs';
import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { onError } from '@apto-catalog-frontend/store/configuration/configuration.actions';

@Injectable()
export class AuthMessageBusService {
	public constructor(private messageBusService: MessageBusService, private store: Store) {}

  public resetPassword(email: string): Observable<MessageBusResponse<boolean>> {
    return this.command('ResetPassword', [{ email }]);
  }

  public updatePassword(password: string, repeatPassword: string, token: string): Observable<MessageBusResponse<boolean>> {
    return this.command('ChangePasswordWithToken', [{ password, repeatPassword, token }]);
  }

	private command<Result>(command: string, args: any[]): Observable<Result> {
		return this.messageBusService.command<Result>(command, args).pipe(
			filter((response) => {
				if (response.message.error) {
          this.store.dispatch(onError({ message: response.message }));
				}
				return !response.message.error;
			}),
			map((response) => response.result)
		);
	}
}
