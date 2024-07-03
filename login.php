l<?php
	/*
		codigos de erro:
			0: falha de autenticação
			1: usuário já existente
			2: falha no banco de dados
			3: faltam parâmetros
			4: entrada não encontrada no DB
	*/

	require_once('conexao_db.php');     /*Chama a conexao com o DB*/
	require_once('autenticacao.php');   /*Chama a autenticao*/
	
	$resposta = array();                /*Array contendo a resposta da requisicao*/
	
        /*Autenticado*/
	if (autenticar($db_con))
	{
		$resposta["Success"] = 1;
	}
	
        /*Nao Autenticado*/
	else
	{
		$resposta["Success"] = 0;
		$resposta["Error"] = "User ou senha incorreto";
		$resposta["Error_code"] = 0;
	}
	
	$db_con = null;                 /*Fecha conexao com o DB*/
	
	echo json_encode($resposta);    /*Ecoa resposta no formato json*/
	
?>