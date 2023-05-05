export interface MessageBusResponseMessage {
	messageName: string;
	message: string;
	date: string;
	duration: number;
	error: true;
	errorType: string;
	errorPayload: {
		product: string;
		section: string;
		element: string;
		property: string;
		value: number;
	};
	uuid: string;
	url: string;
}

export interface MessageBusResponse<Result> {
	message: MessageBusResponseMessage;
	result: Result;
}
