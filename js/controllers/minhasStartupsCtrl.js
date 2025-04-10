app.controller("minhasStartupsCtrl", [
  "$scope",
  "$http",
  "$window",
  "$location",
  "$rootScope",
  function ($scope, $http, $window, $location, $rootScope) {
    $scope.idusuario = localStorage.getItem("startwe_idusuario");
    $scope.usuario = localStorage.getItem("startwe_usuario");
    console.log(`id usuario ${$scope.idusuario}, usuario ${$scope.usuario}`);

    if (location.hostname == "localhost") {
      var urlPrefix =
        "http://localhost:8888/web/startWe/api/registerStartup.php";
      var urlOptionPrefix =
        "http://localhost:8888/web/startWe/api/registerStartup.php?option=";
    } else {
      var urlPrefix = "api/registerStartup.php";
      var urlOptionPrefix = "api/registerStartup.php?option=";
    }

    var pegarMinhasStartups = function () {
      var option = "pegar minhas startups";
      var idusuario = $scope.idusuario;

      $http
        .get(urlOptionPrefix + option + "&idusuario=" + idusuario)
        .then(function (response) {
          // console.log(response);
          $scope.startups = response.data;
        });
    };
    pegarMinhasStartups();
  },
]);
