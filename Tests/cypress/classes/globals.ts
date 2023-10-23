export const ViewportPresets = {
  mobile: { width: 360, height: 800 }, // Galaxy s21
  tablet: { width: 768, height: 1024 }, // iPad dimensions
  desktop: { width: 1920, height: 1200 }, // Standard desktop dimensions
};

// eslint-disable-next-line no-shadow
export enum ViewportPresetsEnum {
  MOBILE = 'mobile',
  TABLET = 'tablet',
  DESKTOP = 'desktop'
}

// eslint-disable-next-line no-shadow
export enum ExistingLanguages {
  ENGLISH = 'en_GB',
  GERMAN = 'de_DE',
  FRENCH = 'fr_FR',
}

export const ExistingLanguageTranslations = {
  ENGLISH: {
    de_DE: 'German',
    en_GB: 'English',
    fr_FR: 'French',
  },
  GERMAN: {
    de_DE: 'Deutsch',
    en_GB: 'Englisch',
    fr_FR: 'Französisch',
  },
  FRENCH: {
    de_DE: 'Allemand',
    en_GB: 'Anglais',
    fr_FR: 'Français',
  },
};

// eslint-disable-next-line no-shadow
export enum UserRoleEnum {
  ADMIN = 'admin',
  USER = 'user'
}
