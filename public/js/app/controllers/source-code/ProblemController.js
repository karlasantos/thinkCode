module.exports = function ($scope, $http, SweetAlert, NgTableParams) {
    $scope.problemsListTools = {
        lastSelected: null,

        tableParams: new NgTableParams({},
            {
                getData: function(params){
                    var paramPageCount = params.url(),
                    sorting = params.sorting(),
                    paramsUrl = [];
                    var address = '/api/problem/';

                    if(paramPageCount.page !== undefined)
                        paramsUrl.push('page=' + paramPageCount.page);

                    if(paramPageCount.count !== undefined)
                        paramsUrl.push('count=' + paramPageCount.count);

                    for (var k in sorting) {
                        paramsUrl.push( 'sort='+ k +':'+ sorting[k] );
                    }

                    address = address +"?"+ paramsUrl.join("&");

                    return $http.get(address)
                        .then(function onSuccess(response) {
                            params.total(response.data.total);
                            return response.data.problems;
                        }, function onError(response) {
                            params.total(0);
                            return [];
                        });
                }
            }
        ),

        select: function(item)
        {
            window.location.href = '/problem/view/?id='+item.id;
        },

        changeSelection: function(item)
        {
            if($scope.problemsListTools.lastSelected !== null && $scope.problemsListTools.lastSelected !== item) {
                $scope.problemsListTools.lastSelected.$selected = false;
                item.$selected = ! item.$selected;
            } else if ($scope.problemsListTools.lastSelected == null){
                item.$selected = ! item.$selected;
            }

            if (item.$selected !== false) {
                $scope.problemsListTools.lastSelected = item;
            } else {
                $scope.problemsListTools.lastSelected = null;
            }

            this.select(item);
            console.log(item);
            console.log($scope.problemsListTools.lastSelected );
        }
    };

    $scope.problemSelectedTools = {
        problem: {
            id: null,
            title: null,
            description: null,
            categoryName: null,
            resolved: false,
        },

        initData: function(id)
        {
            if(id != null) {
                $http.get('/api/problem/' + id)
                    .then(function onSuccess(response) {
                        $scope.problemSelectedTools.problem = response.data.problem;
                    }, function onError(response) {
                        $scope.problemSelectedTools.problem = null;
                    });
            }
        },

        submitProblem: function () {
            window.location.href = '/source-code/source-code/submission?problemId='+$scope.problemSelectedTools.problem.id;
        }
    }
};