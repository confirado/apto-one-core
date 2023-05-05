import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface ContentSnippet {
	name: string;
	content: TranslatedValue;
	children?: ContentSnippet[];
}
