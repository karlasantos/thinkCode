require('angular');

var MainController = require('./controllers/MainController');
var AuthController = require('./controllers/user/AuthController');

angular.module('app', []);
// angular.module('app').controller('MainController', ['$scope', MainController]);
angular.module('app').controller('AuthController', ['$scope', '$http', AuthController]);