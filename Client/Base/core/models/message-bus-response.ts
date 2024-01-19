import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface SingleErrorPayload {
  id: string;
  name: string;
  softRule: boolean;
  errorMessage: TranslatedValue;
  explain: string;
}

export interface MessageBusResponseMessage {
	messageName: string;
	message: string;
	date: string;
	duration: number;
	error: true;
	errorType: string;
	errorPayload: SingleErrorPayload[];
	uuid: string;
	url: string;
}

export interface MessageBusResponse<Result> {
	message: MessageBusResponseMessage;
	result: Result;
}
