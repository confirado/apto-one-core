import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { CyHttpMessages } from 'cypress/types/net-stubbing';

export type Payload = {
  arguments?: any[],
  query: string
}

export enum HttpRequestTypes {
  POST = 'post',
  GET = 'get',
}

export interface IRequestData {
  alias: string,
  endpoint?: string,
  payload?: Payload,
  method?: HttpRequestTypes,
  // expectedResponse?: MessageBusResponse<any>
}

export enum UserTypes {
  SUPERADMIN = 'superadmin',
}

export type UserFixture = {
  [key in UserTypes]: {
    username: string;
    password: string;
    email: string;
  };
};

export type CustomInterceptionResponse<Result> = CyHttpMessages.IncomingResponse & MessageBusResponse<Result>;
