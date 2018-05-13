module.exports = function ($scope, $http, SweetAlert) {
    $scope.submissionTools = {
        problem: [],
        code: null,
        searchLanguage: null,
        // list of `state` value/display objects
        languages        : null,


        initData: function(id)
        {
            if(id != null) {
                $http.get('/api/source-code/problem/' + id+'?session=true')
                    .then(function onSuccess(response) {
                        $scope.submissionTools.problem = response.data.result;
                    }, function onError(response) {
                        $scope.submissionTools.problem = null;
                    });
            }
        },

        // ******************************
        // Internal methods
        // ******************************

        /**
         * Search for languages... use $timeout to simulate
         * remote dataservice call.
         */
        querySearchLanguage: function(query) {
            var address, request;
            if(query) {
                address = '/api/source-code/language' + ('?search='+query.toLowerCase());
            }
            else {
                address = '/api/source-code/language'
            }

            console.log(query);
            request = $http.get(address);
            return request.then(function onSuccess(response) {
                return response.data.results;
            }, function onError(response) {
                return [];
            });
        },

        selectedLanguageChange: function(language) {
            $scope.submissionTools.problem.languageId = language.id;
        },

    };
};