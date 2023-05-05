import IndexActions from './actions/index.actions';
import IndexAuthActions from './actions/index-auth.actions';

// actions must be an angular factory
const AptoFrontendActions = [
    IndexActions,
    IndexAuthActions
];

export default AptoFrontendActions;