import AptoExtendProvider from './services/apto-extend.provider';
import AptoReducersProvider from './services/apto-reducers.provider';
import MessageBusFactory from './services/message-bus.factory';
import XDomainRequestFactory from './services/x-domain-request.factory';
import LanguageFactory from './services/language.provider';
import SnippetFactory from './services/snippet.factory';

const AptoFrontendServices = {
    factories: [
        MessageBusFactory,
        XDomainRequestFactory,
        SnippetFactory
    ],
    services: [],
    provider: [
        LanguageFactory,
        AptoReducersProvider,
        AptoExtendProvider
    ]
};

export default AptoFrontendServices;