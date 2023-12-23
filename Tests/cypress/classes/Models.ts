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

export enum RequestTypes {
  QUERY = 'query',
  COMMAND = 'command',
  REQUEST = 'request',
}

export interface IRequestData {
  alias: string,
  payload?: Payload,
  endpoint?: string,
  method?: HttpRequestTypes,
  type?: RequestTypes,
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
