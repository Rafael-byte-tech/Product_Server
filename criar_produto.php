<?php
	//	OK	Conectar com o DB.
	//	OK	Verificar se todos os dados do novo user vieram na request.
	//	OK	Verificar se esse novo user existe no DB.
	//	OK	Inserir o user no DB.
	//	OK	Retornar uma resposta.
	
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
		if (isset($_POST['nome']) && isset($_POST['preco']) && isset($_POST['descricao']) && isset($_FILES['img']))
		{	
			$nome = trim($_POST['nome']);
			$preco = $_POST['preco'];
			$descricao = $_POST['descricao'];
			
			/*Abrir arquivo e carregar em memoria*/
			$local_arquivo = $_FILES['img']['tmp_name'];
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
			
			$consulta_insert_produto = $db_con->prepare("INSERT INTO produtos(nome, preco, descricao, img, usuarios_login) VALUES('$nome', '$preco', '$descricao', '$img_imgur_url', '$login')");
			if ($consulta_insert_produto->execute())
			{
				$resposta["Success"] = 1;
			}
			else
			{
				$resposta["Success"] = 0;
				$resposta["Error"] = "Erro ao criar produto no DB";
				$resposta["Error_code"] = 2;
			}
		}
		
		else 
		{
			$resposta["Success"] = 0;
			$resposta["Error"] = "There is no BAND! Some required parameters are missing.";
			$resposta["Error_code"] = 3;
		}
	}
	
	else
	{
		$resposta["Success"] = 0;
		$resposta["Error"] = "user ou senha incorretos";
		$resposta["Error_code"] = 0;
	}
	
	/*Fecha conexao com o servidor*/
	$db_con = null;
	
	/*Envia resposta*/
	echo json_encode($resposta);
?>