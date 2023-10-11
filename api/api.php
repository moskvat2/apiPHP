<?php

require 'database.php';

// Rota para buscar todas as tarefas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    try {
        $stmt = $conn->query('SELECT * FROM tasks');
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Rota para adicionar uma nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // if (empty($data[0]['title'])) {
    //     echo json_encode(['error' => 'O titulo da tarefa e obrigatorio']);
    //     exit;
    // }

    //$title = $data['title'];

    try {
        for($i=0; $i < sizeof($data); $i++){
            // echo $data[$i]['title'];
            $title = $data[$i]['title'];
            $completed = $data[$i]['completed'];

            $sql = "INSERT INTO tasks(title, completed) VALUES(:title, :completed)";
            $stmt = $conn->prepare( $sql );
            $stmt->bindParam( ':title', $title );
            $stmt->bindParam( ':completed', $completed );
            $result = $stmt->execute();

            // $stmt = $conn->prepare('INSERT INTO tasks(title, completed) VALUES (:title, :completed)');
            // $stmt->bindParam(':title', $title, $completed);
            // $stmt->execute();
            // $taskId = $conn->lastInsertId();
             //echo json_encode(['id' => $taskId, 'title' => $title, 'completed' => $completed]);
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
        exit;
    }

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

// Rota para deletar uma tarefa
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }

    $taskId = $data['id'];

    try {
        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

?>