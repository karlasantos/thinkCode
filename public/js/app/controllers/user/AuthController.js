module.exports = function ($scope, $http) {
    $scope.recoverPasswordTools = {
        model: {
            email: null
        },
        result: {
            success: null,
            error: null
        },
        hideAlert: false,
        loading: false,

        recoverPassword: function () {
            //todo fazer função de validação
            if ($scope.recoverPasswordTools.model != null) {
                $scope.recoverPasswordTools.loading = true;

                $http.post('/api/recover-password', $scope.recoverPasswordTools.model)
                    .then(function onSuccess(response) {
                        // Handle success
                        var data = response.data;
                        // var status = response.status;
                        // var statusText = response.statusText;
                        // var headers = response.headers;
                        // var config = response.config;

                        $scope.recoverPasswordTools.model.email = null;
                        if (data != null) {
                            $scope.recoverPasswordTools.result.success = data.result;
                            $scope.recoverPasswordTools.result.error = null;
                            $scope.recoverPasswordTools.loading = false;
                            $scope.recoverPasswordTools.hideAlert = false;
                        }
                    }, function onError(response) {

                        // Handle error
                        var data = response.data;
                        // var status = response.status;
                        // var statusText = response.statusText;
                        // var headers = response.headers;
                        // var config = response.config;
                        if (data != null) {
                            $scope.recoverPasswordTools.result.error = data.result;
                            $scope.recoverPasswordTools.result.success = null;
                            $scope.recoverPasswordTools.loading = false;
                            $scope.recoverPasswordTools.hideAlert = false;
                        }
                    });
            }
        }
    };

    $scope.registerTools = {
        user: {
            fullName: null,
            birthday: null,
            school: null,
            gender: 'Feminino',
            email: null,
            password: null,
            passwordConfirm: null
        },
        result: {
            success: null,
            error: null
        },
        hideAlert: false,
        loading: false,

        createUser: function () {
            //todo fazer função de validação
            if($scope.registerTools.user.fullName != null) {
                $scope.registerTools.loading = true;
                $http.post('/api/register-user', $scope.registerTools.user)
                    .then(function onSuccess(response) {
                        // Handle success
                        var data = response.data;
                        if (data != null) {
                            $scope.registerTools.result.success = data.result;
                            $scope.registerTools.result.error = null;
                            $scope.registerTools.loading = false;
                            $scope.registerTools.hideAlert = false;
                        }
                    }, function onError(response) {
                        // Handle error
                        var data = response.data;
                        if (data != null) {
                            //todo tratar melhor esses errors, fazer um service para isso
                            $scope.registerTools.result.error = data.result;
                            $scope.registerTools.result.success = null;
                            $scope.registerTools.loading = false;
                            $scope.registerTools.hideAlert = false;
                        }
                    });
            }
        },
    }
};