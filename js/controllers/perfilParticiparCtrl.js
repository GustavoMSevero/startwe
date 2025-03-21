app.controller("perfilParticiparCtrl", [
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
        "http://localhost:8888/web/startWe/api/perfilParticipante.php";
      var urlPrefixUploadCV =
        "http://localhost:8888/web/startWe/api/uploadCurriculo.php";
    } else {
      var urlPrefix = "api/perfilParticipante.php";
      var urlPrefixUploadCV = "api/uploadCurriculo.php";
    }

    $(document).ready(function () {
      $("#cep").focusout(function () {
        var valorCep = $("#cep").val();
        $http
          .get("http://viacep.com.br/ws/" + valorCep + "/json/")
          .then(function (response) {
            // console.log(response.data);
            if (response.data.erro == true) {
              $scope.erro = "CEP inexistente! Tente novamente.";
            } else {
              $scope.participante.cep = response.data.cep;
              $scope.participante.logradouro = response.data.logradouro;
              $scope.participante.localidade = response.data.localidade;
              $scope.participante.uf = response.data.uf;
              $scope.participante.estado = response.data.estado;
            }
          });
      });
    });

    $scope.cadastrarParticipante = function (participante) {
      participante.option = "cadastrar perfil participante";
      participante.idusuario = $scope.idusuario;
      participante.nome = $scope.usuario;
      // console.log(participante);
      $http.post(urlPrefix, participante).then(function (response) {
        // console.log(response.data);
        if (response.data.status == 1) {
          $scope.msg = response.data.msg;
        } else {
          $location.path("/feed");
        }
      });
    };
  },
]);
