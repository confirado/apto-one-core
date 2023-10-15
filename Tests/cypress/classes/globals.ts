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
  ENGLISH = 'Englisch',
  GERMAN = 'Deutsch',
  FRENCH = 'Französisch',
}

// eslint-disable-next-line no-shadow
export enum UserRoleEnum {
  ADMIN = 'admin',
  USER = 'user'
}
