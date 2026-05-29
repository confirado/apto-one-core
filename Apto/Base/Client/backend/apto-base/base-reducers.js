import IndexReducer from './reducers/index.reducer';
import DomainEventLogReducer from './reducers/domain-event-log.reducer';
import LanguageReducer from './reducers/language.reducer';
import MessageBusFirewallReducer from './reducers/message-bus-firewall.reducer';
import UserRoleReducer from './reducers/user-role.reducer';
import UserReducer from './reducers/user.reducer';
import MediaReducer from './reducers/media.reducer';
import CustomerReducer from './reducers/customer.reducer';
import CustomerGroupReducer from './reducers/customer-group.reducer';
import ContentSnippetReducer from './reducers/content-snippet.reducer';
import CustomPropertyReducer from './reducers/custom-property.reducer';
import SettingsReducer from './reducers/settings.reducer';

// reducers must be an angular provider
const AptoBackendReducers = [
    IndexReducer,
    DomainEventLogReducer,
    LanguageReducer,
    MessageBusFirewallReducer,
    UserRoleReducer,
    UserReducer,
    MediaReducer,
    CustomerReducer,
    CustomerGroupReducer,
    ContentSnippetReducer,
    CustomPropertyReducer,
    SettingsReducer
];

export default AptoBackendReducers;
