import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';
import { environment } from '@apto-frontend/src/environments/environment';

@Injectable()
export class FrontendUserRepository {
  private readonly api = environment.api;

  public constructor(private messageBus: MessageBusService, private http: HttpClient) {}

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
            externalCustomerGroupId: response.user.externalCustomerGroupId,
            customerNumber: response.user.customerNumber
          };
        }
      )
    );
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
            userName: response.user.userName,
            email: response.user.email,
            externalCustomerGroupId: response.user.externalCustomerGroupId,
            customerNumber: response.user.customerNumber
          };
        }
      )
    );
  }
}
