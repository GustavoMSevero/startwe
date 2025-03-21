<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}



switch ($option) {

	case 'pegar usuario para editar':

		$idusuario = $_GET['idusuario'];

		$getUser=$pdo->prepare("SELECT * FROM user WHERE id=:idusuario");
		$getUser->bindValue(":idusuario", $idusuario);
		$getUser->execute();

		$return = array();

		try {

			while ($linha=$getUser->fetch(PDO::FETCH_ASSOC)) {

				$cpf = $linha['cpf'];
				$sexo = $linha['sexo'];
				$dataNascimento = $linha['dataNascimento'];
				$cidade = $linha['cidade'];
				$email = $linha['email'];
				$usuario = $linha['usuario'];
				$senha = $linha['senha'];

				$dataNascimentoP = explode('-', $dataNascimento);
				$dataNascimento = $dataNascimentoP[2].'/'.$dataNascimentoP[1].'/'.$dataNascimentoP[0];
	
				$return = array(
					'cpf'	=> $cpf,
					'sexo'	=> $sexo,
					'dataNascimento'	=> $dataNascimento,
					'cidade'	=> $cidade,
					'email'	=> $email,
					'usuario'	=> $usuario
				);
	
			}
	
			echo json_encode($return);


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	case 'mostrar foto':

		$idusuario = $_GET['idusuario'];
		$idfoto = 0;
		$imagem = '';
		$nome = '';

		$qryMostrarFoto=$pdo->prepare('SELECT * FROM fotoUsuario WHERE idusuario=:idusuario');
		$qryMostrarFoto->bindValue('idusuario', $idusuario);
		$qryMostrarFoto->execute();

		$return = array();

		while ($linha=$qryMostrarFoto->fetch(PDO::FETCH_ASSOC)) {

			$idfoto = $linha['idfoto'];
			$nome = $linha['nome'];
			$imagem = "api/uploadFoto/".$nome;

		}
			
			if($imagem == ""){
				$imagem = "./images/avatars/admin.png";
				//./images/avatars/admin.png

				$return = array(
					'imagem' => $imagem
				);

			} else {

				$return = array(
					'idfoto' => $idfoto,
					'imagem' => $imagem
				);

			}

			echo json_encode($return);


		break;

	case "atualizar usuario":
		//var_dump($data);
		$cpf = $data->cpf;
		$sexo = $data->sexo;
		$dataNascimento = $data->dataNascimento;
		$cidade = $data->cidade;
		$email = $data->email;
		$usuario = $data->usuario;
		$idusuario = $data->idusuario;

		$dataNascimentoP = explode('/', $dataNascimento);
		$dataNascimento = $dataNascimentoP[2].'-'.$dataNascimentoP[1].'-'.$dataNascimentoP[0];

        try {
            $updateUser=$pdo->prepare("UPDATE user SET cpf=:cpf, sexo=:sexo, dataNascimento=:dataNascimento, 
			cidade=:cidade, email=:email, usuario=:usuario WHERE id=:idusuario");
            $updateUser->bindValue(':cpf', $cpf);
            $updateUser->bindValue(':sexo', $sexo);
			$updateUser->bindValue(':dataNascimento', $dataNascimento);
			$updateUser->bindValue(':cidade', $cidade);
			$updateUser->bindValue(':email', $email);
			$updateUser->bindValue(':usuario', $usuario);
            $updateUser->bindValue(':idusuario', $idusuario);
			$updateUser->execute();

			$status = 1;
			$msg = "Dados atualizados com Sucesso.";

            $return = array(
                'status' => $status,
                'usuario' => $usuario
            );

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

		break;
	
	default:
		# code...
		break;
}




?>