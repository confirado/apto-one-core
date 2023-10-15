import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';

export type Payload = {
  arguments?: any[],
  query: string
}

export interface IRequestData {
  alias: string,
  endpoint: string,
  payload?: Payload,
  response?: MessageBusResponse<any>
}
