import { FormControl } from '@angular/forms';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface MaterialPickerFilter {
	colorRating: string | null;
	priceGroup: string | null;
	properties: string[] | null;
	searchString: string | null;
}

export interface MaterialPickerFilterForm {
	colorRating: FormControl<string | null>;
	priceGroup: FormControl<string | null>;
	properties: any;
	searchString: FormControl<string | null>;
}

export interface MaterialPickerColor {
	name: string;
	hex: string;
  visibleHex?: string;
	inPool: boolean;
}

export interface MaterialPickerPriceGroup {
	id: string;
	name: TranslatedValue;
}

export interface Material {
  id: string;
  active: boolean;
  isNotAvailable: boolean;
  identifier: null;
  name: TranslatedValue;
  description: TranslatedValue;
  clicks: number;
  reflection: null;
  transmission: null;
  absorption: null;
  created: string;
  properties: MaterialProperty[];
  previewImage: {
    id: string;
    directory: string;
    filename: string;
    extension: string;
    path: string;
    fileUrl: string;
  };
  galleryImages: GalleryImages[];
  colorRatings: [
    {
      id: string;
      color: string;
      rating: string;
    }
  ];
}

export interface GalleryImages {
  directory: string;
  extension: string;
  fileUrl: string;
  filename: string;
  id: string;
  path: string;
}

export interface CustomProperty {
  key: string;
  value: string;
  surrogateId: string;
}

export interface Group {
  id: string;
  identifier: string;
  name: TranslatedValue;
  position: number;
}

export interface MaterialProperty {
  customProperties: CustomProperty[];
  group: Group;
  name: TranslatedValue;
}

export interface MaterialPickerItem {
	id: string;
	created: string;
	surrogateId: string;
	pool: {
		id: string;
		name: TranslatedValue;
	};
	material: Material;
	priceGroup: {
		id: string;
		name: TranslatedValue;
		additionalCharge: number;
	};
}
export interface Property {
	id: string;
	name: TranslatedValue;
	isDefault: number;
}

export interface PropertyGroup {
	id: string;
	name: TranslatedValue;
	allowMultiple: boolean;
	properties: Property[];
}
