import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { environment } from '@apto-frontend/src/environments/environment';
import { Observable } from 'rxjs';

class MessageTypeNotFoundError {
  private readonly code: string = 'message-type-not-found';
  private readonly type: string;

  public constructor(type: string) {
    this.type = type;
  }

  public getType(): string {
    return this.type;
  }
}

@Injectable()
export class MessageBusService {
	private readonly api = environment.api;

	public constructor(private http: HttpClient) {}

	public query<Result>(query: string, args: any[]): Observable<MessageBusResponse<Result>> {
		return this.post('query', {
			arguments: args,
			query
		});
	}

	public command<Result>(command: string, args: any[]): Observable<MessageBusResponse<Result>> {
		return this.post('command', {
			arguments: args,
			command
		});
	}

	public post(type: string, body: any): Observable<any> {

    switch (type) {
      case 'query': {
        return this.http.post(this.api.query, body);
      }
      case 'command': {
        return this.http.post(this.api.command, body);
      }
      default: {
        throw new MessageTypeNotFoundError(type);
      }
    }
	}
}
