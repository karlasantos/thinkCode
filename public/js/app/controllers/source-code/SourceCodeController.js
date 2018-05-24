module.exports = function ($scope, $http, SweetAlert, cytoData) {
    $scope.options = { //See http://js.cytoscape.org/#core/initialisation for core options
        textureOnViewport:true,
        pixelRatio: 'auto',
        motionBlur: false,
        hideEdgesOnViewport:true
    };

    $scope.layout = {name: 'cose', padding: 50};   //See http://js.cytoscape.org/#collection/layout for available layouts and options

    $scope.cy_graph_ready = function(evt){
        console.log('graph ready to be interacted with: ', evt);
    };

    $scope.elements = {
        j: { group:'nodes', data: { id: 'j', name: 'Jerry', weight: 65, faveColor: '#6FB1FC', faveShape: 'triangle' }, position: {x: 10, y:50} },
        e: { group:'nodes', data: { id: 'e', name: 'Elaine', weight: 45, faveColor: '#EDA1ED', faveShape: 'ellipse' } },
        k: { group:'nodes', data: { id: 'k', name: 'Kramer', weight: 75, faveColor: '#86B342', faveShape: 'octagon' } },
        g: { group:'nodes', data: { id: 'g', name: 'George', weight: 70, faveColor: '#F5A45D', faveShape: 'rectangle' } },

        e1: { group:'edges', data: { source: 'j', target: 'e', faveColor: '#6FB1FC', strength: 90 } },
        e2: { group:'edges', data: { source: 'j', target: 'k', faveColor: '#6FB1FC', strength: 70 } },
        e3: { group:'edges', data: { source: 'j', target: 'g', faveColor: '#6FB1FC', strength: 80 } },

        e4: { group:'edges', data: { source: 'e', target: 'j', faveColor: '#EDA1ED', strength: 95 } },
        e5: { group:'edges', data: { source: 'e', target: 'k', faveColor: '#EDA1ED', strength: 60 }, classes: 'questionable' },

        e6: { group:'edges', data: { source: 'k', target: 'j', faveColor: '#86B342', strength: 100 } },
        e7: { group:'edges', data: { source: 'k', target: 'e', faveColor: '#86B342', strength: 100 } },
        e8: { group:'edges', data: { source: 'k', target: 'g', faveColor: '#86B342', strength: 100 } },

        e9: { group:'edges', data: { source: 'g', target: 'j', faveColor: '#F5A45D', strength: 90 } }
    };

    $scope.style = [ // See http://js.cytoscape.org/#style for style formatting and options.
        {
            selector: 'node',
            style: {
                'shape': 'data(faveShape)',
                'width': 'mapData(weight, 40, 80, 20, 60)',
                'content': 'data(name)',
                'text-valign': 'center',
                'text-outline-width': 2,
                'text-outline-color': 'data(faveColor)',
                'background-color': 'data(faveColor)',
                'color': '#fff'
            }
        },
        {
            selector: 'selected',
            style: {
                'border-width': 3,
                'border-color': '#333'
            }
        },
        {
            selector: 'edge',
            style: {
                'curve-style': 'bezier',
                'opacity': 0.666,
                'width': 'mapData(strength, 70, 100, 2, 6)',
                'target-arrow-shape': 'triangle',
                'source-arrow-shape': 'circle',
                'line-color': 'data(faveColor)',
                'source-arrow-color': 'data(faveColor)',
                'target-arrow-color': 'data(faveColor)'
            }
        },
        {
            selector: 'edge.questionable',
            style: {
                'line-style': 'dotted',
                'target-arrow-shape': 'diamond'
            }
        },
        {
            selector: '.faded',
            style: {
                'opacity': 0.25,
                'text-opacity': 0
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