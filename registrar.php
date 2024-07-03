<?php
	/*
		codigos de erro:
			0: falha de autenticação
			1: usuário já existente
			2: falha no banco de dados
			3: faltam parâmetros
			4: entrada não encontrada no DB
	*/
	
	/*Conecta com o DB*/
	require_once('conexao_db.php');
	
	/*Array que retorna a resposta*/
	$resposta = array();

	if (isset($_POST['novo_login']) && isset($_POST['nova_senha']))
	{	
		$novo_login = trim($_POST['novo_login']);
		$nova_senha = trim($_POST['nova_senha']);
		
		$consulta_usuario_existe = $db_con->prepare("SELECT login FROM usuarios WHERE login = '$novo_login'");
		$consulta_usuario_existe->execute();
		
		if ($consulta_usuario_existe->rowCount() > 0)
		{
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "User atualmente cadastrado";
			$resposta["codigo_erro"] = 1;
		}
		
		else
		{
			$token = password_hash($nova_senha, PASSWORD_DEFAULT);
			$consulta_insert = $db_con->prepare("INSERT INTO usuarios (login, token) VALUES ('$novo_login', '$token')");
			
			if ($consulta_insert->execute())
			{
				$resposta["sucesso"] = 1;
			}
			
			else
			{
				$resposta["sucesso"] = 0;
				$resposta["erro"] = "Erro no DB" . $consulta_insert->error;
				$resposta["codigo_erro"] = 2;
			}
		}
	}
	
	else 
	{
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "Faltam params";
		$resposta["codigo_erro"] = 3;
	}
	
	$db_con = null;
	
	echo json_encode($resposta);
?>