import update from 'immutability-helper';

const MessageBusFirewallReducerInject = ['AptoReducersProvider'];
const MessageBusFirewallReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_MESSAGE_BUS_FIREWALL_';
    const initialState = {
        pageHeaderConfig: {
            title: 'MessageBus Firewall',
            toggleSideBarRight: {
                show: true
            }
        },
        aclMessagesRequired: {
            commands: ['AddAclPermission', 'RemoveAclPermission'],
            queries: ['FindAclEntriesByAclClass', 'FindUserRoles']
        },
        messageBusMessages: {
            commands: [],
            queries: []
        },
        aclEntries: [],
        aclEntriesByClass: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.messageBusFirewall = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('MESSAGE_BUS_MESSAGES_RECEIVED'):
                newState = update(state, {
                    messageBusMessages: {
                        commands: {
                            $set: []
                        },
                        queries: {
                            $set: []
                        }
                    }
                });

                for (let commandName in action.payload.commands) {
                    if (!action.payload.commands.hasOwnProperty(commandName)) {
                        return;
                    }

                    newState.messageBusMessages.commands.push({
                        commandName: commandName,
                        commandClass: action.payload.commands[commandName]
                    })
                }

                for (let queryName in action.payload.queries) {
                    if (!action.payload.queries.hasOwnProperty(queryName)) {
                        return;
                    }

                    newState.messageBusMessages.queries.push({
                        queryName: queryName,
                        queryClass: action.payload.queries[queryName]
                    })
                }
                return newState;
            case getType('ACL_ENTRIES_RECEIVED'):
                newState = update(state, {
                    aclEntries: {
                        $set: action.payload
                    }
                });
                return newState;

            case getType('FETCH_ACL_ENTRIES_BY_ACL_CLASS_FULFILLED'):
                const result = action.payload.data.result.data;
                const aclClass = action.payload.data.result.aclClass;

                let messageClasses = {};
                messageClasses[aclClass] = [];

                newState = update(state, {
                    aclEntriesByClass: {
                        $merge: messageClasses
                    }
                });

                if (result.length < 1) {
                    return newState;
                }

                for (let i = 0; i < result.length; i++) {
                    messageClasses[aclClass].push(result[i].role);
                }

                newState = update(newState, {
                    aclEntriesByClass: {
                        $merge: messageClasses
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('messageBusFirewall', this.messageBusFirewall);

    this.$get = function() {};
};

MessageBusFirewallReducer.$inject = MessageBusFirewallReducerInject;

export default ['MessageBusFirewallReducer', MessageBusFirewallReducer];