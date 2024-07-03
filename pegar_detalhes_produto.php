<?php
/*------------------------------------------------------------------------------*/
/*
 Script 1
 
 Nome: pegar_detalhes_produto.php

 Função: obtém os dados referentes a um produto específico.
  
 Método: GET
  
 Autenticação: SIM
 
 Parâmetros de entrada:
    id->indica o id do produto requerido pelo cliente.

Exemplo de Resposta Positiva:
 {
    "sucesso":1,
    "nome":"Produto 1- teste",
    "preco":"100.00",
    "descricao":"Descricao do produto 1",
    "criado_por":"danielrt",
    "criado_em":"2023-09-26 02:38:14 +0000",
    "img":"https:\/\/i.imgur.com\/kS76v0M.jpg"
 }
 
 Exemplo de Resposta Negativa:
 {
    "sucesso":0,
    "erro":"faltam parametros",
    "cod_erro":3
 } 
 */

/*
codigos de erro:
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
    
    $resposta = array();
    
    if (autenticar($db_con))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        /*Verifica parametros*/
        if (isset($id))
        {   
            /*Prepara consulta do DB*/
            $consulta_pegar_linha = $db_con->prepare("SELECT * FROM produtos WHERE id = '$id'");
            
            /*Tentativa de executar consulta do DB*/
            if ($consulta_pegar_linha->execute())
            {
                /*Faz um Array da entrada do DB*/
                $linha = $consulta_pegar_linha->fetch(PDO::FETCH_ASSOC);
                
                /*Verifica se a entrada existe no DB*/
                if ($linha)
                {
                    $resposta["Sucesso"]    =   1;
                    $resposta["Nome"]       =   $linha["nome"];
                    $resposta["Preco"]      =   $linha["preco"];
                    $resposta["Descricao"]  =   $linha["descricao"];
                    $resposta["Criado_por"] =   $linha["usuarios_login"];
                    $resposta["Criado_em"]  =   $linha["criado_em"];
                    $resposta["Img"]        =   $linha["img"];
                }
                
                /*Entrada nao existe no DB*/
                else 
                {
                    $resposta["Sucesso"] = 0;
                    $resposta["Erro"] = "Entrada nao encontrada no DB";
                    $resposta["cod_erro"] = 4;
                }
                
            }
            
            /*Nao eh  possivel executar consulta*/
            else
            {
		$resposta["Sucesso"] = 0;
		$resposta["Erro"] = "Erro DB" . $consulta_insert->error;
		$resposta["cod_erro"] = 2;
            }
        }
        
        /*Faltam parametros para a requisicao*/
        else
        {
            $resposta["sucesso"] = 0;
            $resposta["Erro"] = "Faltam parametros";
            $resposta["cod_erro"] = 3;
        }
    }
    
    /*Falha de autenticacao*/
    else
    {
        $resposta["Sucesso"] = 0;
        $resposta["Erro"] = "User ou senha incorretos";
        $resposta["cod_erro"] = 0;
    }
    
    $db_con = null;                 /*Fecha conexao com o DB*/
	
    echo json_encode($resposta);    /*Envia json contendo resposta*/
?>