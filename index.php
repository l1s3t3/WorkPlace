<?php

require_once 'code.php';
require_once 'Employee.php';
require_once 'Task.php';


require 'ex8/vendor/autoload.php';

$errors = [];
$command = $_GET["command"] ?? 'dashboard';
$status = $_GET["status"] ?? null;

$firstName = $_POST['firstName'] ?? null;
$lastName = $_POST['lastName'] ?? null;
$target_file = $_POST['target_file'] ?? null;
$isCompletedBlock = null;

$isCompleted = "";
$completed = "";
$description = $_POST['description'] ?? null;
$estimate = $_POST['estimate'] ?? null;
$employeeId = $_POST['employeeId'] ?? null;
$employeeId = !empty($employeeId) ? $employeeId : "";


if ($command === "employee_form") {

    if (isset($_POST["submitButton"])) {

        $target_dir = "uploads/";
        if (!empty($_FILES["picture"]["name"])) {
            $target_file = $target_dir . basename($_FILES["picture"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        } else {
            $target_file = "";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
        }

        if (!empty($_GET['employee-id'])) {
            $firstNameUpdate = $_POST['firstName'];
            $lastNameUpdate = $_POST['lastName'];
            $employee = new Employee($_GET['employee-id'], $firstNameUpdate, $lastNameUpdate, $target_file);
            updateEmployee($employee);

            header('Location: index.php?command=employee_list&status=success');
            exit();
        }
    }

    if (!empty($_GET['employee-id'])) {
        $emp = findByEmployeeId($_GET['employee-id']);
        if ($emp) {
            $firstName = $emp->firstName;
            $lastName = $emp->lastName;
            $target_file = $emp->target_file;
        }


        $deleteButton = true;

        if (isset($_POST["deleteButton"])) {
            deleteByEmployeeId($_GET['employee-id']);
            header('Location: index.php?command=employee_list&status=success');
            exit();
        }
    }

    if (!(isset($_GET['employee-id'])) && isset($_POST['submitButton']) && !(!$firstName
            || count(str_split($firstName)) > 21) && !(count(str_split($lastName)) < 2
            || count(str_split($lastName)) > 22)) {

        $employee = new Employee(null, $firstName, $lastName, $target_file);
        saveEmployee($employee);

        header('Location: index.php?command=employee_list&status=success');
        exit();
    } else {
        if (isset($_POST['submitButton'])) {
            $employee = new Employee(null, $firstName, $lastName, $target_file);
            $errors = validateEmployeeName($employee);

        }
    }

    $data = [
        'errors' => $errors,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'target_file' => $target_file,
        'deleteButton' => $deleteButton ?? null,
    ];

    renderPage('employee-form.latte', $data);

} else if ($command === "employee_list") {

    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            $isCompletedBlock = true;
        } else {
            $isCompletedBlock = false;
        }
    }

    $data = ['isCompletedBlock' => $isCompletedBlock ?? null, 'employees' => getAllEmployees()];

    renderPage('employee-list.latte', $data);

} else if ($command === "task_form") {

    if (!empty($_GET['task-id'])) {
        $task = findByTaskId($_GET['task-id']);

        if ($task) {
            $description = $task->description;
            $estimate = $task->estimate;
            $completed = $task->completed;
            $employeeId = $task->employeeId;
        }
    }

    if (isset($_GET['task-id'])) {

        $deleteButton = true;
        $isChecked = ($completed === '1') ? 'checked' : '';
        $isCompleted = true;

        if (isset($_POST["deleteButton"])) {
            deleteByTaskId($_GET['task-id']);
            header('Location: index.php?command=task_list&status=success');
            exit();
        }

        if (isset($_POST['submitButton'])) {
            $descriptionUpdate = $_POST['description'];
            $estimateUpdate = $_POST['estimate'];
            $completedUpdate = isset($_POST['isCompleted']) && $_POST['isCompleted'] === 'yes'? '1' : '0';
            $employeeId = $_POST['employeeId'] ?? null;

            $task = new Task($_GET['task-id'], $descriptionUpdate, $estimateUpdate, $completedUpdate, $employeeId);
            updateTask($task);

            header('Location: index.php?command=task_list&status=success');
            exit();
        }
    }

    if (!(isset($_GET['task-id'])) && isset($_POST['submitButton']) && !(strlen($description) < 5 || strlen($description) > 40)) {
        $completed = isset($_POST['isCompleted']) && $_POST['isCompleted'] === 'yes' ? '1' : '0';

        $task = new Task(null, $description, $estimate, $completed, $employeeId);
        saveTask($task);

        header('Location: index.php?command=task_list&status=success');
        exit();
    } else {
        if (isset($_POST['submitButton'])) {
            $task = new Task(null, $description, $estimate, $completed, $employeeId);
            $errors = validateTaskDescription($task);

        }
    }
    $data = [
            'errors' => $errors,
        'description' => $description,
        'estimate' => $estimate,
        'completed' => $completed,
        'employees' => getAllEmployees(),
        'employeeId' => $employeeId,
        'deleteButton' => $deleteButton ?? null,

    ];

    renderPage('task-form.latte', $data);

} else if ($command === "task_list") {

    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            $isCompletedBlock = true;
        } else {
            $isCompletedBlock = false;
        }
    }

    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        $task->status = $task->completed == "1" ? "Closed" : ($task->employeeId ? "Pending" : "Open");
    }

    $data = ['isCompletedBlock' => $isCompletedBlock ?? null, 'tasks' => $tasks];

    renderPage('task-list.latte', $data);

} else if ($command === "dashboard") {

    $employees = getAllEmployees();
    $conn = getConnection();

    foreach ($employees as $employee) {
        $stmt = $conn->prepare("SELECT COUNT(*) as task_count FROM tasks WHERE employee_id = :employee_id");
        $stmt->execute([':employee_id' => $employee->id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $employee->task_count = $result['task_count'];
    }

    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        $task->status = $task->completed == "1" ? "Closed" : ($task->employeeId ? "Pending" : "Open");
    }

    $data = ['employees' => $employees, 'tasks' => $tasks,];

    renderPage('dashboard.latte', $data);
}

function validateEmployeeName(Employee $employee): array {
    $errors = [];
    if (!$employee->firstName || strlen($employee->firstName) > 21) {
        $errors[] = 'First name must be 1 to 21 characters.';
    }
    if (strlen($employee->lastName) < 2 || strlen($employee->lastName) > 22) {
        $errors[] = 'Last name must be 2 to 22 characters.';
    }
    return $errors;
}

function validateTaskDescription(Task $task): array {
    $errors = [];
    if (strlen($task->description) < 5 || strlen($task->description) > 40) {
        $errors[] = 'Description must be 5 to 40 characters.';
    }
    return $errors;
}


function renderPage(string $subTemplate, array $data): void {
    $latte = new Latte\Engine;
    $latte->render("$subTemplate", [...$data]);
}