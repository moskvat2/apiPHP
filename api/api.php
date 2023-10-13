<?php

require 'database.php';

$error_return = json_encode(['params' => 'incorrect params']);

// Rota para buscar todas as tarefas - busca 1 item ou todos os itens
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)){
        $sql = 'SELECT * FROM tasks';
    }else{
        if(is_int($data['id'])){
            $sql = "SELECT * FROM tasks WHERE id = ". $data['id'];
        }else{
            echo json_encode($error_return);
            exit;
        }
    }

    try {
        $stmt = $conn->query($sql);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($tasks)){
            echo json_encode(['error' => 'No result']);
            exit;
        }else{
            header('Content-Type: application/json');
            echo json_encode($tasks);
        }
    }catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }

}

// Rota para adicionar uma nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        for($i=0; $i < sizeof($data); $i++){
            $title = $data[$i]['title'];
            $completed = $data[$i]['completed'];

            $sql = "INSERT INTO tasks(title, completed) VALUES(:title, :completed)";
            $stmt = $conn->prepare( $sql );
            $stmt->bindParam( ':title', $title );
            $stmt->bindParam( ':completed', $completed );
            $result = $stmt->execute();
            $taskId = $conn->lastInsertId();
            echo json_encode(['id' => $taskId, 'title' => $title, 'completed' => $completed]);
        }

    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Rota para marcar uma tarefa como concluída
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
    }else{
        $taskId = $data['id'];

        try {
            $stmt = $conn->prepare('UPDATE tasks SET completed = 1 WHERE id = :id');
            $stmt->bindParam(':id', $taskId);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

// Rota para deletar uma tarefa
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }else{
        if(is_int($data['id'])){
           $taskId = $data['id'];

            try {
                $stmt = $conn->prepare('DELETE FROM tasks WHERE id = :id');
                $stmt->bindParam(':id', $taskId);
                $stmt->execute();
                echo json_encode(['success' => 'Registro deletado com sucesso']);
            } catch(PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            } 
        }else{
            echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
            exit;
        }
        
    }
}

?>