app.controller("registerCtrl", [
  "$scope",
  "$http",
  "$window",
  "$location",
  "$rootScope",
  function ($scope, $http, $window, $location, $rootScope) {
    $scope.admin = {};

    if (location.hostname == "localhost") {
      var urlPrefix = "http://localhost:8888/web/startWe/api/register.php";
    } else {
      var urlPrefix = "api/register.php";
    }

    $scope.createUser = function (user) {
      user.option = "cadastro";
      $http.post(urlPrefix, user).then(function (response) {
        if (response.data.exists == 1) {
          $scope.alerta = response.data.msg;
        } else {
          if (typeof Storage !== "undefined") {
            localStorage.setItem("startwe_usuario", response.data.usuario);
            localStorage.setItem("startwe_idusuario", response.data.idusuario);
          }

          $location.path("/seleciona-perfil");
        }
      });
    };
  },
]);
