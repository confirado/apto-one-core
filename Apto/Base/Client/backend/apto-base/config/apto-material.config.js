import moment from 'moment';

const AptoMaterialConfigInject = ['$mdThemingProvider', '$mdIconProvider', '$mdDateLocaleProvider'];
const AptoMaterialConfig = function ($mdThemingProvider, $mdIconProvider, $mdDateLocaleProvider) {
    $mdThemingProvider.definePalette('apto-grey', {
        '50': '#fafafa',
        '100': '#f5f5f5',
        '200': '#eeeeee',
        '300': '#e0e0e0',
        '400': '#bdbdbd',
        '500': '#9e9e9e',
        '600': '#757575',
        '700': '#616161',
        '800': '#424242',
        '900': '#212121',
        'A100': '#ffffff',
        'A200': '#000000',
        'A400': '#303030',
        'A700': '#616161',
        'contrastDefaultColor': 'dark',
        'contrastLightColors': '600 700 800 900 A200 A400 A700'
    });

    $mdThemingProvider.definePalette('apto-red', {
        '50': '#ffebee',
        '100': '#ffcdd2',
        '200': '#ef9a9a',
        '300': '#e57373',
        '400': '#ef5350',
        '500': '#f44336',
        '600': '#e53935',
        '700': '#d32f2f',
        '800': '#c62828',
        '900': '#b71c1c',
        'A100': '#ff8a80',
        'A200': '#ff5252',
        'A400': '#ff1744',
        'A700': '#d50000',
        'contrastDefaultColor': 'light',
        'contrastDarkColors': '50 100 200 300 A100',
        'contrastStrongLightColors': '400 500 600 700 A200 A400 A700'
    });

    $mdThemingProvider.definePalette('apto-green', {
        '50': '#e8f5e9',
        '100': '#c8e6c9',
        '200': '#a5d6a7',
        '300': '#81c784',
        '400': '#66bb6a',
        '500': '#4caf50',
        '600': '#43a047',
        '700': '#388e3c',
        '800': '#2e7d32',
        '900': '#1b5e20',
        'A100': '#b9f6ca',
        'A200': '#69f0ae',
        'A400': '#00e676',
        'A700': '#00c853',
        'contrastDefaultColor': 'dark',
        'contrastLightColors': '500 600 700 800 900',
        'contrastStrongLightColors': '500 600 700'
    });

    $mdThemingProvider.definePalette('apto-blue', {
        '50': '#e3f2fd',
        '100': '#bbdefb',
        '200': '#90caf9',
        '300': '#64b5f6',
        '400': '#42a5f5',
        '500': '#14548c',   // confirado
        '600': '#1e88e5',
        '700': '#1976d2',
        '800': '#072540',   // confirado
        '900': '#0d47a1',
        'A100': '#82b1ff',
        'A200': '#14548c',  // confirado
        'A400': '#2979ff',
        'A700': '#2962ff',
        'contrastDefaultColor': 'light',
        'contrastDarkColors': '50 100 200 300 400 A100',
        'contrastStrongLightColors': '500 600 700 A200 A400 A700'
    });

    $mdThemingProvider.definePalette('apto-blue-dark', {
        '50': '#e3f2fd',
        '100': '#bbdefb',
        '200': '#90caf9',
        '300': '#64b5f6',
        '400': '#42a5f5',
        '500': '#14548c',   // confirado
        '600': '#1e88e5',
        '700': '#1976d2',
        '800': '#072540',   // confirado
        '900': '#0d47a1',
        'A100': '#ffffff',  // from grey
        'A200': '#000000',  // from grey
        'A400': '#303030',  // from grey
        'A700': '#616161',  // from grey
        'contrastDefaultColor': 'light',
        'contrastDarkColors': '50 100 200 300 400 A100',
        'contrastStrongLightColors': '500 600 700 A200 A400 A700'
    });

    $mdThemingProvider.definePalette('apto-blue-grey', {
        '50': '#eceff1',
        '100': '#cfd8dc',
        '200': '#b0bec5',
        '300': '#90a4ae',
        '400': '#78909c',
        '500': '#607d8b',
        '600': '#546e7a',
        '700': '#455a64',
        '800': '#37474f',
        '900': '#263238',
        'A100': '#cfd8dc',
        'A200': '#b0bec5',
        'A400': '#78909c',
        'A700': '#455a64',
        'contrastDefaultColor': 'light',
        'contrastDarkColors': '50 100 200 300 A100 A200',
        'contrastStrongLightColors': '400 500 700'
    });

    $mdThemingProvider.theme('default')
        .primaryPalette('apto-green')
        .accentPalette('apto-blue')
        .warnPalette('apto-red')
        .backgroundPalette('apto-grey');

    $mdThemingProvider.theme('apto-dark', 'default')
        .primaryPalette('apto-green')
        .accentPalette('apto-blue-dark')
        .warnPalette('apto-red')
        .backgroundPalette('apto-blue-dark')
        .dark();

    $mdIconProvider.defaultFontSet('fa');

    $mdDateLocaleProvider.firstDayOfWeek = 1;

    $mdDateLocaleProvider.parseDate = function(dateString) {
        let m = moment(dateString, 'DD.MM.YYYY', true);
        return m.isValid() ? m.toDate() : new Date(NaN);
    };

    $mdDateLocaleProvider.formatDate = function(date) {
        let m = moment(date);
        return m.isValid() ? m.format('DD.MM.YYYY') : '';
    };
};
AptoMaterialConfig.$inject = AptoMaterialConfigInject;

export default ['AptoMaterialConfig', AptoMaterialConfig];