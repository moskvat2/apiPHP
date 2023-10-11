<?php

	$host = '45.65.222.182';
	$db = 'tasks';
	$port = 3306;
	$user = 'gerson';
	$pass = 'gerson';

	try {
		$conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "Conexao feita com sucesso!";
	} catch(PDOException $e) {
		echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
		exit;
	}

?>