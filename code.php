<?php

require_once 'ex6/connection.php';
require_once 'Employee.php';
require_once 'Task.php';


function getAllEmployees(): array {
    $conn = getConnection();
    $stmt = $conn->query("SELECT * FROM employees");
    $employees = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $employees[] = new Employee($row['id'], $row['first_name'], $row['last_name'], $row['target_file']);
    }
    return $employees;
}


function findByEmployeeId(string $id): ?Employee {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return new Employee($row['id'], $row['first_name'], $row['last_name'], $row['target_file']);
    } else {
        return null;
    }
}

function deleteByEmployeeId(string $id): void {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = :id");
    $stmt->execute([':id' => $id]);
}


function updateEmployee(Employee $employee): void {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE employees SET first_name = :first_name, last_name = :last_name, target_file = :target_file WHERE id = :id");
    $stmt->execute([
        ':first_name' => $employee->firstName,
        ':last_name' => $employee->lastName,
        ':target_file' => $employee->target_file,
        ':id' => $employee->id
    ]);
}

function saveEmployee(Employee $employee): void {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, target_file) 
                        VALUES (:first_name, :last_name, :target_file)");
    $stmt->execute([
        ':first_name' => $employee->firstName,
        ':last_name' => $employee->lastName,
        ':target_file' => $employee->target_file
    ]);
}


function getAllTasks(): array {
    $conn = getConnection();
    $stmt = $conn->query("SELECT * FROM tasks");
    $tasks = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tasks[] = new Task($row['id'], $row['description'], $row['estimate'], $row['completed'], $row['employee_id']);
    }
    return $tasks;
}

function findByTaskId(string $id): ?Task {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return new Task($row['id'], $row['description'], $row['estimate'], $row['completed'], $row['employee_id']);
    } else {
        return null;
    }
}


function updateTask(Task $task): void {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE tasks SET description = :description, estimate = :estimate, completed = :completed, employee_id = :employee_id WHERE id = :id");
    $stmt->execute([
        ':description' => $task->description,
        ':estimate' => $task->estimate,
        ':completed' => $task->completed,
        ':employee_id' => $task->employeeId,
        ':id' => $task->id
    ]);
}


function deleteByTaskId(string $id): void {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $id]);
}


function saveTask(Task $task): void {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO tasks (description, estimate, completed, employee_id) VALUES (:description, :estimate, :completed, :employee_id)");
    $stmt->execute([
        ':description' => $task->description,
        ':estimate' => $task->estimate,
        ':completed' => $task->completed,
        ':employee_id' => $task->employeeId
    ]);
}

