module.exports = function ($scope, $http, SweetAlert, $timeout, $q, $log) {
    $scope.accountTools = {
        user: {
            id: null,
            email: null,
            oldPassword: null,
            newPassword: null,
            passwordConfirm: null,
            changePassword: false,
            profile: {
                fullName: null,
                birthday: null,
                school: null,
                gender: 'Feminino',
            },
        },

        searchLanguage: null,
        // list of `state` value/display objects
        languages        : null,

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
            $scope.accountTools.user.profile.defaultLanguageId = language.id;
        },

        initData: function(id)
        {
            if(id != null) {
                $http.get('/api/user/' + id)
                    .then(function onSuccess(response) {
                        $scope.accountTools.user = response.data.result;
                        $scope.accountTools.changePassword = false;
                        $scope.accountTools.clearPassword();
                        $scope.accountTools.languages = $scope.accountTools.querySearchLanguage(null);
                    }, function onError(response) {
                        $scope.accountTools.user = null;
                    });
            }
        },

        update: {
            loading: false,

            save: function ()
            {
                //todo fazer função de validação
                if ($scope.accountTools.user.id != null) {
                    $scope.accountTools.loading = true;
                    $http.put('/api/user/' + $scope.accountTools.user.id, $scope.accountTools.user)
                        .then(function onSuccess(response) {
                            // Handle success
                            var data = response.data;
                            if (data != null) {
                                $scope.accountTools.update.loading = false;
                                console.log($scope.accountTools.update.result);
                                SweetAlert.swal("Salvo", "As configurações de sua conta foram salvas com sucesso!", "success");
                            }
                        }, function onError(response) {
                            // Handle error
                            var data = response.data;
                            if (data != null) {
                                //todo tratar melhor esses errors, fazer um service para isso
                                $scope.accountTools.update.loading = false;
                                SweetAlert.swal("Erro", data.result, "error");
                                console.log($scope.accountTools.update.result);
                            }
                        });
                }
            },
        },

        clearPassword: function ()
        {
            $scope.accountTools.user.oldPassword = null;
            $scope.accountTools.user.newPassword = null;
            $scope.accountTools.user.passwordConfirm = null;
        },

        cancelSettings: function ()
        {
            window.location.href = '/home';
        }
    }
};