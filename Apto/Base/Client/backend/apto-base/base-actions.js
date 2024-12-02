import IndexActions from './actions/index.actions';
import PageHeaderActions from './actions/page-header.actions';
import DomainEventLogActions from './actions/domain-event-log.actions';
import LanguageActions from './actions/language.actions';
import DataListActions from './actions/data-list.actions';
import MessageBusActions from './actions/message-bus.actions';
import MessageBusFirewallActions from './actions/message-bus-firewall.actions';
import UserRoleActions from './actions/user-role.actions';
import UserActions from './actions/user.actions';
import MediaActions from './actions/media.actions';
import CustomerActions from './actions/customer.actions';
import CustomerGroupActions from './actions/customer-group.actions';
import ContentSnippetActions from './actions/content-snippet.actions';
import CustomPropertyActions from './actions/custom-property.actions';
import SettingsActions from './actions/settings.actions';

// actions must be an angular factory
const AptoBackendActions = [
    IndexActions,
    PageHeaderActions,
    DomainEventLogActions,
    LanguageActions,
    DataListActions,
    MessageBusActions,
    MessageBusFirewallActions,
    UserRoleActions,
    UserActions,
    MediaActions,
    CustomerActions,
    CustomerGroupActions,
    ContentSnippetActions,
    CustomPropertyActions,
    SettingsActions
];

export default AptoBackendActions;
