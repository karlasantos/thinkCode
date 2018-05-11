require('angular');
require('sweetalert');
require('angular-sweetalert');
require('ng-table');

var cytoscape = require('cytoscape');

var MainController = require('./controllers/MainController');
var AuthController = require('./controllers/user/AuthController');
var UserController = require('./controllers/user/UserController');
var ProblemController = require('./controllers/source-code/ProblemController');

angular.module('app', ['oitozero.ngSweetAlert', 'ngTable']);
angular.module('app').controller('MainController', ['$scope', MainController]);
angular.module('app').controller('AuthController', ['$scope', '$http', AuthController]);
angular.module('app').controller('UserController', ['$scope', '$http', 'SweetAlert', UserController]);
angular.module('app').controller('ProblemController', ['$scope', '$http', 'SweetAlert', 'NgTableParams', ProblemController]);
