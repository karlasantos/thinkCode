require('angular');
require('sweetalert');
require('angular-sweetalert');

var cytoscape = require('cytoscape');
var MainController = require('./controllers/MainController');
var AuthController = require('./controllers/user/AuthController');
var UserController = require('./controllers/user/UserController');

angular.module('app', ['oitozero.ngSweetAlert', 'cytoscape']);
angular.module('app').controller('MainController', ['$scope', MainController]);
angular.module('app').controller('AuthController', ['$scope', '$http', AuthController]);
angular.module('app').controller('UserController', ['$scope', '$http', 'SweetAlert', UserController]);
