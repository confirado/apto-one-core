import moment from 'moment';
import DomainEventLogListTemplate from './domain-event-log-list.html';
import DomainEventLogFilterTemplate from './domain-event-log-filter.html';

const DomainEventLogControllerInject = ['$scope', '$ngRedux', '$templateCache', 'DomainEventLogActions', 'IndexActions'];
const DomainEventLogController = function($scope, $ngRedux, $templateCache, DomainEventLogActions, IndexActions) {
    $templateCache.put('base/pages/domain-event-log/domain-event-log-list.html', DomainEventLogListTemplate);
    $templateCache.put('base/pages/domain-event-log/domain-event-log-filter.html', DomainEventLogFilterTemplate);

    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.domainEventLog.pageHeaderConfig,
            queryFilter: state.domainEventLog.queryFilter,
            groupedTypeNames: state.domainEventLog.groupedTypeNames,
            groupedUsers: state.domainEventLog.groupedUsers,
            domainEvents: state.domainEventLog.domainEvents
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        domainEventsFetch: DomainEventLogActions.domainEventsFetch,
        groupedTypeNamesFetch: DomainEventLogActions.groupedTypeNamesFetch,
        groupedUsersFetch: DomainEventLogActions.groupedUsersFetch,
        resetQueryFilter: DomainEventLogActions.resetQueryFilter,
        toggleSidebarRight: IndexActions.toggleSidebarRight
    })($scope);

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.domainEventsFetch(
                    page,
                    $scope.pageHeaderConfig.pagination.recordsPerPage,
                    $scope.queryFilter
                );
            }
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    init();

    function init() {
        initFilter();
        $scope.groupedUsersFetch();
        $scope.groupedTypeNamesFetch();
        $scope.domainEventsFetch(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            $scope.queryFilter
        );
    }

    function initFilter() {
        let fromDate = moment($scope.queryFilter.fromDate),
            toDate = moment($scope.queryFilter.toDate);

        $scope.filter = {
            groupedTypeNamesSearch: '',
            groupedUsersSearch: '',
            maxDate: new Date(),
            userIds: angular.copy($scope.queryFilter.userIds),
            typeNames: angular.copy($scope.queryFilter.typeNames),
            eventBody: $scope.queryFilter.eventBody,
            fromDate: null,
            toDate: null
        };

        if (fromDate.isValid()) {
            $scope.filter.fromDate = fromDate.toDate();
        }

        if (toDate.isValid()) {
            $scope.filter.toDate = toDate.toDate();
        }
    }

    function setFilter() {
        let fromDate = moment($scope.filter.fromDate),
            toDate = moment($scope.filter.toDate),
            queryFilter = {
                typeNames: $scope.filter.typeNames,
                userIds: $scope.filter.userIds,
                eventBody: $scope.filter.eventBody,
                fromDate:  null,
                toDate: null
            };

        if (fromDate.isValid()) {
            queryFilter.fromDate =  fromDate.format('YYYY-MM-DD')
        }

        if (toDate.isValid()) {
            queryFilter.toDate =  toDate.format('YYYY-MM-DD')
        }

        $scope.domainEventsFetch(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            queryFilter
        );
    }

    function resetFilter() {
        $scope.resetQueryFilter();
        initFilter();
        $scope.domainEventsFetch(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            $scope.queryFilter
        );
    }

    $scope.setFilter = setFilter;
    $scope.resetFilter = resetFilter;
    $scope.$on('$destroy', subscribedActions);
};

DomainEventLogController.$inject = DomainEventLogControllerInject;

export default ['DomainEventController', DomainEventLogController];