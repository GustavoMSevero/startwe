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

	case 'cadastrar perfil participante':
        // print_r($data);
        $profissao = $data->profissao;
        $sobre = $data->sobre;
        $cep = $data->cep;
        $logradouro = $data->logradouro;
        $estado = $data->estado;
		$linkedin = $data->linkedin;
        $localidade = $data->localidade;
        $uf = $data->uf;
        $idusuario = $data->idusuario;
		$nome = $data->nome;

		try {

			$searchUser=$pdo->prepare("SELECT * FROM perfilParticipante WHERE nome=:nome");
			$searchUser->bindValue(":nome", $nome);
			$searchUser->execute();

			$exists = $searchUser->rowCount();

			$return = array();

			if($exists == 1){

                $msg = utf8_encode('Perfil já existente');

				$return = array(
                    'status' => 1,
                    'msg' => $msg
				);

				echo json_encode($return);

            } else {

                $registerUserPerfil=$pdo->prepare("INSERT INTO perfilParticipante (idusuario, nome, 
                profissao, sobre, cep, logradouro, localidade, uf, estado, urlLinkedin) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $registerUserPerfil->bindValue(1, $idusuario);
                $registerUserPerfil->bindValue(2, $nome);
                $registerUserPerfil->bindValue(3, $profissao);
                $registerUserPerfil->bindValue(4, $sobre);
                $registerUserPerfil->bindValue(5, $cep);
                $registerUserPerfil->bindValue(6, $logradouro);
                $registerUserPerfil->bindValue(7, $localidade);
                $registerUserPerfil->bindValue(8, $uf);
                $registerUserPerfil->bindValue(9, $estado);
                $registerUserPerfil->bindValue(10, $linkedin);
                $registerUserPerfil->execute();

            }

			
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

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
					'usuario'	=> $usuario,
					'senha'	=> $senha
				);
	
			}
	
			echo json_encode($return);


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	case "atualizar usuario":
        //var_dump($data);
		$idusuario = $data->idusuario;
		$cpf = $data->cpf;
		$sexo = $data->sexo;
		$dataNascimento = $data->dataNascimento;
		$cidade = $data->cidade;
		$email = $data->email;
		$usuario = $data->usuario;
        $senha = $data->senha;
        @$novaSenha = $data->novaSenha;

        if($novaSenha == ''){
            //echo 'Não tem senha nova';
            $senha;
        } else {
            //echo 'senha nova '.$novaSenha;
            $senha = md5($novaSenha);
        }

        $dataNascimentoP = explode('/', $dataNascimento);
        $dataNascimento = $dataNascimentoP[2].'-'.$dataNascimentoP[1].'-'.$dataNascimentoP[0];

        //echo $cpf.' '.$sexo.' '.$dataNascimento.' '.$cidade.' '.$email.' '.$usuario.' '.$senha;

        try {
            $updateUser=$pdo->prepare("UPDATE user SET cpf=:cpf, sexo=:sexo, dataNascimento=:dataNascimento, cidade=:cidade,
            email=:email, usuario=:usuario, senha=:senha WHERE id=:idusuario");
            $updateUser->bindValue(':cpf', $cpf);
            $updateUser->bindValue(':sexo', $sexo);
            $updateUser->bindValue(':dataNascimento', $dataNascimento);
            $updateUser->bindValue(':cidade', $cidade);
            $updateUser->bindValue(':email', $email);
            $updateUser->bindValue(':usuario', $usuario);
            $updateUser->bindValue(':senha', $senha);
            $updateUser->bindValue(':idusuario', $idusuario);
			$updateUser->execute();

			$status = 1;
			$msg = "Dados atualizados com Sucesso.";

            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

		break;

	case 'verifica se tem cadastro participante':

		$idusuario = $_GET['idusuario'];

		try {

			$checkForParticpant=$pdo->prepare("SELECT * FROM perfilParticipante WHERE idusuario=:idusuario");
			$checkForParticpant->bindValue(":idusuario", $idusuario);

			$checkForParticpant->execute();

			$exists = $checkForParticpant->rowCount();

			if($exists == 0){

				$return = array();
		
					$return = array(
						'checked'	=> false
					);
		
		
				echo json_encode($return);

			} else {

				$return = array();
		
					$return = array(
						'checked'	=> true
					);
		
		
				echo json_encode($return);

			}


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	case 'pegar participante':

		$idusuario = $_GET['idusuario'];

		$getParticipant=$pdo->prepare("SELECT * FROM perfilParticipante WHERE idusuario=:idusuario");
		$getParticipant->bindValue(":idusuario", $idusuario);
		$getParticipant->execute();

		$return = array();

		try {

			while ($linha=$getParticipant->fetch(PDO::FETCH_ASSOC)) {

				// $idPerfilParticipante = $linha['idPerfilParticipante'];
				$nome = $linha['nome'];
				$profissao = $linha['profissao'];
				$sobre = $linha['sobre'];
				$cep = $linha['cep'];
				$logradouro = $linha['logradouro'];
				$localidade = $linha['localidade'];
				$estado = $linha['estado'];
				$uf = $linha['uf'];
				$urlLinkedin = $linha['urlLinkedin'];
	
				$return = array(
					// 'idPerfilParticipante'	=> $idPerfilParticipante,
					'idusuario'	=> $idusuario,
					'nome'	=> $nome,
					'profissao'	=> $profissao,
					'sobre'	=> $sobre,
					'cep'	=> $cep,
					'logradouro'	=> $logradouro,
					'localidade'	=> $localidade,
					'estado'	=> $estado,
					'uf'	=> $uf,
					'urlLinkedin'	=> $urlLinkedin
				);
	
			}
	
			echo json_encode($return);


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	case "atualizar participante":
        // print_r($data);
		$cep = $data->cep;
		$estado = $data->estado;
		$idusuario = $data->idusuario;
		$nome = $data->nome;
		$profissao = $data->profissao;
		$sobre = $data->sobre;
		$localidade = $data->localidade;
		$logradouro = $data->logradouro;
		$uf = $data->uf;
		$urlLinkedin = $data->urlLinkedin;

        try {
            $updatePerfilParticipant=$pdo->prepare("UPDATE perfilParticipante SET nome=:nome, profissao=:profissao, sobre=:sobre, cep=:cep, logradouro=:logradouro, localidade=:localidade,
            uf=:uf, estado=:estado, urlLinkedin=:urlLinkedin WHERE idusuario=:idusuario");
            $updatePerfilParticipant->bindValue(':nome', $nome);
            $updatePerfilParticipant->bindValue(':profissao', $profissao);
            $updatePerfilParticipant->bindValue(':sobre', $sobre);
            $updatePerfilParticipant->bindValue(':cep', $cep);
            $updatePerfilParticipant->bindValue(':logradouro', $logradouro);
            $updatePerfilParticipant->bindValue(':localidade', $localidade);
            $updatePerfilParticipant->bindValue(':uf', $uf);
            $updatePerfilParticipant->bindValue(':estado', $estado);
            $updatePerfilParticipant->bindValue(':urlLinkedin', $urlLinkedin);
            $updatePerfilParticipant->bindValue(':idusuario', $idusuario);
			$updatePerfilParticipant->execute();

			$status = 1;
			$msg = "Dados atualizados com Sucesso.";

            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

		break;

	case 'pegar perfil':

		$idusuario = $_GET['idusuario'];

		try {

			$getParticipant=$pdo->prepare("SELECT * FROM perfilParticipante WHERE idusuario=:idusuario");
			$getParticipant->bindValue(":idusuario", $idusuario);
			$getParticipant->execute();

			$exists = $getParticipant->rowCount();

			if($exists == 1){

			$return = array();

			while ($linha=$getParticipant->fetch(PDO::FETCH_ASSOC)) {

				// $idPerfilParticipante = $linha['idPerfilParticipante'];
				$nome = $linha['nome'];
				$profissao = $linha['profissao'];
				$sobre = $linha['sobre'];
				$localidade = $linha['localidade'];
				$uf = $linha['uf'];
				$urlLinkedin = $linha['urlLinkedin'];
	
				$return = array(
					// 'idPerfilParticipante'	=> $idPerfilParticipante,
					'idusuario'	=> $idusuario,
					'nome'	=> $nome,
					'profissao'	=> $profissao,
					'sobre'	=> $sobre,
					'localidade'	=> $localidade,
					'uf'	=> $uf,
					'urlLinkedin'	=> $urlLinkedin
				);
	
			}
	
			echo json_encode($return);

		} else {

			$msg = 'Usuário não tem perfil preenchido';
			$return = array(
				'msg'	=> $msg
			);

			echo json_encode($return);

		}


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	case 'has profile':

		$idusuario = $_GET['idusuario'];

		try {

			$getParticipant=$pdo->prepare("SELECT * FROM perfilParticipante WHERE idusuario=:idusuario");
			$getParticipant->bindValue(":idusuario", $idusuario);
			$getParticipant->execute();

			$exists = $getParticipant->rowCount();

			if($exists == 1){

				$return = array(
					'checked'	=> true,
				);
		
				echo json_encode($return);

			} else {

				$msgProfile = 'Você precisa preencher seu Perfil Participante para entrar em contato.';

				$return = array(
					'checked'	=> false,
					'msgProfile' => $msgProfile
				);
		
				echo json_encode($return);

			}


		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		break;

	
	default:
		# code...
		break;
}




?>