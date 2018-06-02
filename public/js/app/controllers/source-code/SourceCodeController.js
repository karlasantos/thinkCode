module.exports = function ($scope, $http, SweetAlert, cytoData) {
    $scope.submissionTools = {
        submissionData: {
            content: null,
            problemId: null,
            languageId: null,
        },
        problem: [],
        code: null,
        searchLanguage: null,
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
            $scope.submissionTools.submissionData.userCompareId = null;
            $scope.submissionTools.submissionData.sourceCodeCompareId = null;

            var oldSelected = $scope.submissionTools.problem.rank.filter(function (rankingObj) {
                return rankingObj.selected !== undefined && rankingObj.selected === true;
            });
            oldSelected = (oldSelected.length > 0)? oldSelected[0] : null;

            if(oldSelected != null) {
                oldSelected.selected = false;
            }

            if(value === true) {
                $scope.submissionTools.problem.rank[index].selected = value;
                $scope.submissionTools.submissionData.userCompareId = $scope.submissionTools.problem.rank[index].sourceCode.user.id;
                $scope.submissionTools.submissionData.sourceCodeCompareId = $scope.submissionTools.problem.rank[index].sourceCode.id;
            }
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
        showAnalysis: false,

        //configurações dos grafos
        options: {
            fit: true,
            textureOnViewport:true,
            pixelRatio: 'auto',
            motionBlur: false,
            hideEdgesOnViewport:true,
            avoidOverlap: true,
        },

        //layout dos grafos
        layout: {name: 'cose', padding: 65},

        cy_graph_ready: function(evt){
            console.log('graph ready to be interacted with: ', evt);
        },

        style: [
            //configurações para os vértices
            {
                selector: 'node',
                style: {
                    'shape': 'ellipse',
                    'width': '100',
                    'height': '45',
                    'content': 'data(name)',
                    'text-valign': 'center',
                    'text-outline-width': 2,
                    'text-outline-color': '#6FB1FC',
                    'text-transform': 'uppercase',
                    'background-color': '#6FB1FC',
                    'color': '#fff'
                }
            },
            //configurações dos nós selecionados
            {
                selector: ':selected',
                style: {
                    'border-width': 3,
                    'border-color': '#333'
                }
            },
            //configurações das arestas
            {
                selector: 'edge',
                style: {
                    "label": "data(label)",
                    'curve-style': 'bezier',
                    'opacity': 0.666,
                    'width': '5',
                    'target-arrow-shape': 'triangle',
                    'line-color': '#6FB1FC',
                    'source-arrow-color': '#6FB1FC',
                    'target-arrow-color': '#6FB1FC'
                }
            },
        ],

        initData: function (data, problemId) {
            $scope.resultAnalysisTools.analysisSourceCodeSubject = data.result.sourceCodeUser;
            $scope.resultAnalysisTools.analysisSourceCodeSystem = data.result.sourceCodeSystem;
            //retorna os novos dados do problema
            $scope.submissionTools.initData(problemId);
        },
    }

    $scope.resultViewTools = {
        analysisSourceCodeSubject: {},
        analysisSourceCodeSystem: {},

        //configurações dos grafos
        options: {
            fit: true,
            textureOnViewport:true,
            pixelRatio: 'auto',
            motionBlur: false,
            hideEdgesOnViewport:true,
            avoidOverlap: true,
        },

        //layout dos grafos
        layout: {name: 'cose', padding: 65},

        cy_graph_ready: function(evt){
            console.log('graph ready to be interacted with: ', evt);
        },

        style: [
            //configurações para os vértices
            {
                selector: 'node',
                style: {
                    'shape': 'ellipse',
                    'width': '100',
                    'height': '45',
                    'content': 'data(name)',
                    'text-valign': 'center',
                    'text-outline-width': 2,
                    'text-outline-color': '#6FB1FC',
                    'text-transform': 'uppercase',
                    'background-color': '#6FB1FC',
                    'color': '#fff'
                }
            },
            //configurações dos nós selecionados
            {
                selector: ':selected',
                style: {
                    'border-width': 3,
                    'border-color': '#333'
                }
            },
            //configurações das arestas
            {
                selector: 'edge',
                style: {
                    "label": "data(label)",
                    'curve-style': 'bezier',
                    'opacity': 0.666,
                    'width': '5',
                    'target-arrow-shape': 'triangle',
                    'line-color': '#6FB1FC',
                    'source-arrow-color': '#6FB1FC',
                    'target-arrow-color': '#6FB1FC'
                }
            },
        ],

        initData: function (problemId) {
            $scope.submissionTools.initData(problemId);

            $http.get('/api/source-code/source-code/'+problemId)
                .then(function onSuccess(response) {
                    $scope.resultViewTools.analysisSourceCodeSubject = response.data.result;
                   // $scope.resultAnalysisTools.showAnalysis = true;


                }, function onError(response) {
                    var data = response.data;
                    $scope.resultViewTools.analysisSourceCodeSubject = {};
                    if (data != null) {
                        SweetAlert.swal("Erro", data.result, "error");
                    }
                });
        },
    }
};