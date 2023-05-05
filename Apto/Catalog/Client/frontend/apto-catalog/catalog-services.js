import RouteAccessFactory from "./services/route-access.factory";
import PersistedPropertiesFactory from './services/persisted-property.factory';
import ConfigurationProvider from './services/configuration.provider';
import RuleProvider from './services/rule.provider';
import ProductRuleProvider from './services/product-rule.provider';
import ElementValuesProvider from './services/element-values.provider';
import UserInputParser from './services/user-input-parser.provider';
import CustomPropertyHelper from './services/custom-property-helper.provider';

const AptoFrontendServices = {
    factories: [
        RouteAccessFactory,
        PersistedPropertiesFactory
    ],
    services: [],
    provider: [
        ElementValuesProvider,
        ConfigurationProvider,
        RuleProvider,
        UserInputParser,
        CustomPropertyHelper,
        ProductRuleProvider
    ]
};

export default AptoFrontendServices;
