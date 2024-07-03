<?php
    $login = null;
    $senha = null;
    $http_auth = filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION', FILTER_SANITIZE_STRING);
	
    /*Metodo para extrair login e senha via mod_php (Apache)*/
    if (isset(filter_input(INPUT_SERVER, 'PHP_AUTH_USER', FILTER_SANITIZE_STRING)))
    {
	$login = filter_input(INPUT_SERVER, 'PHP_AUTH_USER', FILTER_SANITIZE_STRING);
	$senha = filter_input(INPUT_SERVER, 'PHP_AUTH_PW', FILTER_SANITIZE_STRING);
    }
	
    /*Metodo para demais servers*/
    elseif (isset($http_auth))
    {
        if (preg_match('/^basic/i', $http_auth))
        {
            list($login, $senha) = explode(':', base64_decode(substr($http_auth)));
        }
    }
	
    /*Funcao de autenticacao da conexao com DB*/
    function autenticar($db_con)
    {
	$login = trim($GLOBALS['login']);
	$senha = trim($GLOBALS['senha']);
	
	if(!is_null($login))
	{
            $consulta_usuario_existe = $db_con->prepare("SELECT token FROM usuarios WHERE login = '$login'");
            $consulta_usuario_existe->execute();
	}
        
        if ($consulta_usuario_existe->rowCount() > 0) {$linha = $consulta_usuario_existe->fetch(PDO::FETCH_ASSOC);}
        
        if(password_verify($senha, $linha['token'])) {return true;}
		
	return false;
    }