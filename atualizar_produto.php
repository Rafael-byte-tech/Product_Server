<?php
    /*
    codigos de erro:
    0: falha de autenticação
    1: usuário já existente
    2: falha no banco de dados
    3: faltam parâmetros
    4: entrada não encontrada no DB
    */
	
    require_once('conexao_db.php');		/*Conecta com o DB*/
    require_once('autenticacao.php');	/*Processo de autenticar*/
	
    $resposta = array();				/*Array que retorna a resposta*/
	
	if (autenticar($db_con))
	{
            if (isset($_POST['id']) && isset($_POST['novo_nome']) && isset($_POST['novo_preco']) && $_POST['nova_descricao'] && isset($_FILES['nova_img']))
            {	
                $id = $_POST['id'];
		$nome = trim($_POST['novo_nome']);
		$preco = $_POST['novo_preco'];
		$descricao = $_POST['nova_descricao'];
			
                /*-----------------------------------------------------------------------------------------------------------------------------------*/
		/*PEGAR URL DO IMGUR*/
                        
                /*Abrir arquivo e carregar em memoria*/
		$local_arquivo = $_FILES['nova_img']['tmp_name'];
		$handle = fopen($local_arquivo, "r");
		$img_data = fread($handle, filesize($local_arquivo));
			
		/*ID do IMGUR*/
		$client_id = "ce5d3a656e2aa51";	/*nao faca isso*/
			
		/*Como pegar a imagem e mandar pro IMGUR*/
		/*O base64 pega um dado bin e converte pra str*/
		$pvars = array('image' => base64_encode($img_data));
			
		/*Define limite de tempo da requisicao*/
		$timeout = 30;
			
		/*Requisicao CURL*/
		$curl = curl_init();
			
		/*Constroi requisicao*/
                curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
                curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client_ID ' . $client_id));
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
			
		/*Executa requisicao*/
		$out = curl_exec($curl);
		
		/*Fim da requisicao*/
		curl_close($curl);
			
		/*Verifica se a requisicao CURL funcionou*/
		$imgur_res = json_decode($out, true);

		$img_imgur_url = $imgur_res['data']['link'];
                        
                /*-----------------------------------------------------------------------------------------------------------------------------------*/
        	/*Verficar se o usuário que pretende atualizar é o usuário que criou o produto. INACABADO*/
                //$query_user = $db_con->prepare("QUERY pega login do usuário criador");
                        
                /*-----------------------------------------------------------------------------------------------------------------------------------*/
                /*UPDATE QUERY*/
		$query_update_produto = $db_con->prepare("UPDATE produtos SET nome = '$nome', preco = '$preco', descricao = '$descricao', img = '$img_imgur_url' WHERE id = '$id'");
                
		if ($query_update_produto->execute())
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
		$resposta["Erro"] = "There is no BAND! Some required parameters are missing.";
		$resposta["cod_erro"] = 3;
            }
	}
	
	else
	{
            $resposta["Sucesso"] = 0;
            $resposta["Erro"] = "user ou senha incorretos";
            $resposta["cod_erro"] = 0;
	}
	
	/*Fecha conexao com o servidor*/
	$db_con = null;
	
	/*Envia resposta*/
	echo json_encode($resposta);