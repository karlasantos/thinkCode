module.exports = function ($scope, $http, SweetAlert, cytoData) {
    $scope.options = { //See http://js.cytoscape.org/#core/initialisation for core options
        textureOnViewport:true,
        pixelRatio: 'auto',
        motionBlur: false,
        hideEdgesOnViewport:true
    };

    $scope.layout = {name: 'grid'}   //See http://js.cytoscape.org/#collection/layout for available layouts and options

    $scope.cy_graph_ready = function(evt){
        console.log('graph ready to be interacted with: ', evt);
    };

    $scope.elements = {
        n1:{
            group: 'nodes',
            data:{} //Data property mandatory for all elements
        },
        n2:{
            group: 'nodes',
            data:{}
        },
        e1:{
            group:'edges',
            data:{
                target: 'n1',  //Source and Target mandatory for edges.
                source: 'n2'
            }
        }
    };
    $scope.style = [ // See http://js.cytoscape.org/#style for style formatting and options.
        {
            selector: 'node',
            style: {
                'shape': 'ellipse',
                'border-width': 0,
                'background-color': 'blue'
            }
        }
    ];

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
        querySearchLanguage: function(query)
        {
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
        selectedLanguageChange: function(language)
        {
            if(language != null)
                $scope.submissionTools.submissionData.languageId = language.id;
            else
                $scope.submissionTools.submissionData.languageId = null;
        },

        changeComparison: function (index, value)
        {
            var oldSelected = $scope.submissionTools.problem.rank.filter(function (rankingObj) {
                return rankingObj.selected != undefined && rankingObj.selected === true;
            });
            oldSelected = (oldSelected.length > 0)? oldSelected[0] : null;

            if(oldSelected != null) {
                oldSelected.selected = false;
            }

            $scope.submissionTools.problem.rank[index].selected = value;
            $scope.submissionTools.submissionData.userCompareId = $scope.submissionTools.problem.rank[index].user.id;
        },

        /**
         * Realiza a submissão do código fonte
         */
        submitSourceCode: function ()
        {
            if($scope.submissionTools.submissionData.languageId == null) {
                SweetAlert.swal("Atenção", "Informe a Linguagem de Programação do Código.", "error");
            }

            $http.post('/api/source-code/source-code', $scope.submissionTools.submissionData)
                .then(function onSuccess(response) {
                    $scope.resultAnalysisTools.initData(response.data, $scope.submissionTools.problem.id);
                    $scope.resultAnalysisTools.showAnalysis = true;

                }, function onError(response) {
                    var data = response.data;
                    if (data != null) {
                        //todo tratar melhor esses errors, fazer um service para isso
                        SweetAlert.swal("Erro", data.result, "error");
                    }
                });
        }
    };

    $scope.resultAnalysisTools = {
        analysisSourceCodeSubject: {},
        analysisSourceCodeSystem: {},
        showAnalysis: true,

        initData: function (data, problemId) {
            $scope.resultAnalysisTools.analysisSourceCodeSubject = data.result.sourceCodeUser;
            $scope.resultAnalysisTools.analysisSourceCodeSystem = data.result.sourceCodeSystem;
            //retorna os novos dados do problema
            $scope.submissionTools.initData(problemId);
        },
    }
};