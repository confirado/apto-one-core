import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface SelectItem {
	surrogateId: string;
	id: string;
	name: TranslatedValue;
	isDefault: boolean;
	aptoPrices: unknown[];
}
