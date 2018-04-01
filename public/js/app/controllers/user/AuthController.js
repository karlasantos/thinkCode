module.exports = function ($scope, $http) {
    $scope.recoverPassword = {
        model: {
            email: null
        },
        result: {
            success: null,
            error: null
        }
    };

    $scope.recoverPasswordAction = function () {
        if($scope.recoverPassword.model != null) {
            $http.post('/api/recover-password', $scope.recoverPassword.model)
                .then(function onSuccess(response) {
                    // Handle success
                    var data = response.data;
                    // var status = response.status;
                    // var statusText = response.statusText;
                    // var headers = response.headers;
                    // var config = response.config;

                    $scope.recoverPassword.model.email = null;
                    if(data != null) {
                        result.success = data;
                        result.error = null;
                    }
                }, function onError(response) {
                    // Handle error
                    var data = response.data;
                    // var status = response.status;
                    // var statusText = response.statusText;
                    // var headers = response.headers;
                    // var config = response.config;

                    if(data != null) {
                        result.error = data;
                        result.success = null;
                    }
                });
        }
    }
};