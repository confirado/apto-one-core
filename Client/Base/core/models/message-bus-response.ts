import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { MessageBusErrorTypeEnum } from '@apto-base-core/enums/message-bus-error-type.enum';

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
	errorType: MessageBusErrorTypeEnum;
	errorPayload: SingleErrorPayload[] | any;
	uuid: string;
	url: string;
}

export interface MessageBusResponse<Result> {
	message: MessageBusResponseMessage;
	result: Result;
}
