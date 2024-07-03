<?php
        /*Retorna resposta com todos os produtos*/

	/*
		codigos de erro:
			0: falha de autenticação
			1: usuário já existente
			2: falha no banco de dados
			3: faltam parâmetros
			4: entrada não encontrada no DB
	*/
	
	require_once('conexao_db.php');         /*Conecta com o DB*/
	require_once('autenticacao.php');       /*Requisita autenticacao*/
	
	$resposta = array();			/*Array contendo resposta da requisicao*/
	
        /*Verifica autenticacao*/
	if(autenticar($db_con))
	{       
                /*Verifica os parametros*/ 
        	if (isset($_GET['limit']) && isset($_GET['offset']))
		{	
                        /*Trata as variaveis retornadas por $_GET*/
			$limit = trim($_GET['limit']);
			$offset = trim($_GET['offset']);
			
                        /*Prepara consulta no DB*/
			$consulta_pegar_pagina = $db_con->prepare("SELECT * FROM produtos LIMIT " . $limit . " OFFSET " . $offset);
			
                        /*Tentativa de executar a consulta*/
			if ($consulta_pegar_pagina->execute())
			{       
				$nProdutos = $db_con->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
				
				$resposta["Success"] = 1;
				$resposta["Quantidade_produtos"] = $nProdutos;
				$resposta["produtos"] = array();
				
				if ($consulta_pegar_pagina->rowCount() > 0)
				{
					while($linha = $consulta_pegar_pagina->fetch(PDO::FETCH_ASSOC))
					{
						$produto = array(); /*Array contendo info do produto*/
                                                
						$produto["id"] = $linha["id"];
						$produto["nome"] = $linha["nome"];
						$produto["preco"] = $linha["preco"];
						$produto["img"] = $linha["img"];
						
						array_push($resposta["produtos"], $produto);    /*Insere produto no array de produtos*/
					}
				}
			}
			
                        /*Nao eh  possivel executar consulta*/
			else
			{
				$resposta["Success"] = 0;
				$resposta["Error"] = "Erro DB" . $consulta_insert->error;
				$resposta["Error_code"] = 2;
			}
		}
		
                /*Estao faltando parametros*/
		else
		{
			$resposta["Success"] = 0;
			$resposta["Error"] = "Faltam parametros.";
			$resposta["Error_code"] = 3;
		}
	}
	
        /*Falha na autenticacao*/
	else
	{
		$resposta["Success"] = 0;
		$resposta["Error"] = "user ou senha incorretos";
		$resposta["Error_code"] = 0;
	}
	
	$db_con = null;                 /*Fecha conexao com o DB*/
	
	echo json_encode($resposta);    /*Envia json contendo resposta*/
?>