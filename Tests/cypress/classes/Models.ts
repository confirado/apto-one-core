import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { CyHttpMessages } from 'cypress/types/net-stubbing';

export type Payload = {
  arguments?: any[],
  query: string
}

export interface IRequestData {
  alias: string,
  endpoint?: string,
  payload?: Payload,
  expectedResponse?: MessageBusResponse<any>
}

export type CustomInterceptionResponse<Result> = CyHttpMessages.IncomingResponse & MessageBusResponse<Result>;
