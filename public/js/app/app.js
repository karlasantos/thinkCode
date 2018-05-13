require('angular');
require('sweetalert');
require('angular-sweetalert');
require('ng-table');
require('angular-animate');
require('angular-aria');
require('angular-messages');
require('angular-material');

var cytoscape = require('cytoscape');

var MainController = require('./controllers/MainController');
var AuthController = require('./controllers/user/AuthController');
var UserController = require('./controllers/user/UserController');
var ProblemController = require('./controllers/source-code/ProblemController');
var SourceCodeController = require('./controllers/source-code/SourceCodeController');

angular.module('app', ['oitozero.ngSweetAlert', 'ngTable', 'ngMaterial']);
angular.module('app').controller('MainController', ['$scope', MainController]);
angular.module('app').controller('AuthController', ['$scope', '$http', AuthController]);
angular.module('app').controller('UserController', ['$scope', '$http', 'SweetAlert', '$timeout', '$q', '$log', UserController]);
angular.module('app').controller('ProblemController', ['$scope', '$http', 'NgTableParams', ProblemController]);
angular.module('app').controller('SourceCodeController', ['$scope', '$http', 'SweetAlert', SourceCodeController]);
