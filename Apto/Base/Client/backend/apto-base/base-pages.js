import AptoLoginController from './pages/login/login.controller';
import AptoIndexController from './pages/index/index.controller';
import AptoHomeController from './pages/home/home.controller';
import DomainEventLogController from './pages/domain-event-log/domain-event-log.controller';
import LanguageController from './pages/language/language.controller';
import MessageBusFirewallController from './pages/message-bus-firewall/message-bus-firewall.controller';
import UserController from './pages/user/user.controller';
import UserRoleController from './pages/user-role/user-role.controller';
import MediaController from './pages/media/media.controller';
import CustomerController from './pages/customer/customer.controller';
import CustomerGroupController from './pages/customer-group/customer-group.controller';
import ContentSnippetController from './pages/content-snippet/content-snippet.controller';
import SettingsController from './pages/settings/settings.controller';

const AptoBackendPages = [
    AptoLoginController,
    AptoIndexController,
    AptoHomeController,
    DomainEventLogController,
    LanguageController,
    MessageBusFirewallController,
    UserController,
    UserRoleController,
    MediaController,
    CustomerController,
    CustomerGroupController,
    ContentSnippetController,
    SettingsController
];

export default AptoBackendPages;

