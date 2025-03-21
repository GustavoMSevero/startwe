app.controller("selecionarPerfilCtrl", [
  "$scope",
  "$http",
  "$window",
  "$location",
  "$rootScope",
  function ($scope, $http, $window, $location, $rootScope) {
    $scope.id = localStorage.getItem("startwe_idusuario");
    $scope.usuario = localStorage.getItem("startwe_usuario");
    console.log(`id usuario ${$scope.id}, usuario ${$scope.usuario}`);

    if (location.hostname == "localhost") {
      var urlPrefix = "http://localhost:8888/web/startWe/#/register";
    } else {
      var urlPrefix = "";
    }
  },
]);
