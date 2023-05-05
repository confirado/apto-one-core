import LanguageFactory from './services/language.factory';
import MessageBusFactory from './services/message-bus.factory';
import CurrentUserFactory from './services/current-user.factory';
import AclIsGrantedFactory from './services/acl-is-granted.factory';
import AptoReducersProvider from './services/apto-reducers.provider';

const AptoBackendServices = {
    factories: [
        AclIsGrantedFactory,
        LanguageFactory,
        MessageBusFactory,
        CurrentUserFactory
    ],
    services: [],
    provider: [
        AptoReducersProvider
    ]
};

export default AptoBackendServices;