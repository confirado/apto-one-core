import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { AuthMessageBusService } from '@apto-base-frontend/services/auth-message-bus.service';

@Injectable()
export class FrontendUserRepository {
  private readonly api = environment.api;

  public constructor(private authMessageBus: AuthMessageBusService, private http: HttpClient) {}

  public login(username: string, password: string): Observable<FrontendUser | null> {
    return this.http.post(this.api.root + '/login', { username: username, password: password }).pipe(
      map((response: any) => {
          if (!response.user) {
            return null;
          }

          return {
            isLoggedIn: true,
            id: response.user.id,
            userName: response.user.username,
            email: response.user.email,
            customerGroup: response.user.customerGroup,
            customerNumber: response.user.customerNumber
          };
        }
      )
    );
  }

  public resetPassword(email: string): Observable<MessageBusResponse<boolean>> {
    return this.authMessageBus.resetPassword(email);
  }

  public updatePassword(password: string, repeatPassword: string, token: string): Observable<MessageBusResponse<boolean>> {
    return this.authMessageBus.updatePassword(password, repeatPassword, token);
  }

  public logout(): Observable<null> {
    return this.http.post(this.api.root + '/logout', null).pipe(
      map((response: any) => {
          return null;
        }
      )
    );
  }

  public status(): Observable<FrontendUser | null> {
    return this.http.post(this.api.root + '/current-user', null).pipe(
      map((response: any) => {
          if (!response.user) {
            return null;
          }

          return {
            isLoggedIn: true,
            id: response.user.id,
            userName: response.user.username,
            email: response.user.email,
            customerGroup: response.user.customerGroup,
            customerNumber: response.user.customerNumber
          };
        }
      )
    );
  }
}
