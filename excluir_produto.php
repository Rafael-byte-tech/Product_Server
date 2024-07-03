<?php
    
    /*codigos de erro:
    0: falha de autenticação
    1: usuário já existente
    2: falha no banco de dados
    3: faltam parâmetros
    4: entrada não encontrada no DB
    */

/*------------------------------------------------------------------------------*/
    require_once ('conexao_db.php');
    require_once ('autenticacao.php');
/*------------------------------------------------------------------------------*/
    
    $resposta = [];
    
    /*Autenticacao*/
    if (autenticar($db_con))
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        /*Verifica os parametros*/
        if (isset($id))
        {
            /*Prepara consulta do DB*/
            $query_excluir_produto = $db_con->prepare("DELETE FROM produtos WHERE id = '$id'");
            
            if ($query_excluir_produto->execute())
            {
                $resposta["Sucesso"] = 1;
            }
            
            else
            {
		$resposta["Sucesso"] = 0;
		$resposta["Erro"] = "Erro na consulta";
		$resposta["cod_erro"] = 2;
            }
        }
        
        else
        {
            $resposta["Sucesso"] = 0;
            $resposta["Erro"] = "Erro na consulta";
            $resposta["cod_erro"] = 2;
        }
    }
    
    /*Falha de autenticacao*/
    else
    {
        $resposta["Sucesso"] = 0;
        $resposta["Erro"] = "User ou senha incorretos";
        $resposta["cod_erro"] = 0;
    }
    
    /*Fecha conexao com o servidor*/
    $db_con = null;
	
    /*Envia resposta*/
    echo json_encode($resposta);