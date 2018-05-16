module.exports = function ($scope, $http, SweetAlert) {
    $scope.submissionTools = {
        submissionData: {
            content: null,
            problemId: null,
            languageId: null,
        },
        problem: [],
        code: null,
        searchLanguage: null,
        // list of `state` value/display objects
        languages        : null,
        /**
         * Inicializa os dados da submissão
         * @param id int Id de identificação do problema
         */
        initData: function(id)
        {
            if(id != null) {
                $http.get('/api/source-code/problem/' + id+'?session=true')
                    .then(function onSuccess(response) {
                        $scope.submissionTools.problem = response.data.result;
                        $scope.submissionTools.submissionData.problemId = response.data.result.id;
                    }, function onError(response) {
                        $scope.submissionTools.problem = null;
                    });
            }
        },
        /**
         * Pesquisa do select da linguagem de programação do código
         * @param query string parâmetro de pesquisa
         * @returns {*}
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
        /**
         * Seleciona a linguagem de programação do código
         * @param language array de dados da linguagem de programação
         */
        selectedLanguageChange: function(language) {
            if(language != null)
                $scope.submissionTools.submissionData.languageId = language.id;
            else
                $scope.submissionTools.submissionData.languageId = null;
        },
        /**
         * Realiza a submissão do código fonte
         */
        submitSourceCode: function () {
            if($scope.submissionTools.submissionData.languageId == null) {
                SweetAlert.swal("Atenção", "Informe a Linguagem de Programação do Código.", "error");
            }

            //todo enviar o Id do código para a análise de similaridade na requisição

            $http.post('/api/source-code/source-code', $scope.submissionTools.submissionData)
                .then(function onSuccess(response) {

                }, function onError(response) {
                    var data = response.data;
                    if (data != null) {
                        //todo tratar melhor esses errors, fazer um service para isso
                        $scope.accountTools.update.loading = false;
                        SweetAlert.swal("Erro", data.result, "error");
                        console.log($scope.accountTools.update.result);
                    }
                });
        }
    };
};