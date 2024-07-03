<?php
	$host		=	"host = isabelle.db.elephantsql.com;";
	$port		=	"port = 5432;";
	$dbname		=	"dbname = bnphmfru";
	$dbuser		=	"bnphmfru";
	$dbpassword	=	"U_Ts5Q31WE37tgKKbrSqZQ1zoUl3I8uS";
	
	$db_con = new PDO('pgsql:' . $host . $port . $dbname, $dbuser, $dbpassword);
	
	$db_con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>