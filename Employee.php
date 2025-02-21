<?php

class Employee {
    public $id;
    public $firstName;
    public $lastName;
    public $target_file;
    public int $task_count;

    public function __construct($id = null, $firstName = "", $lastName = "", $target_file = "") {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->target_file = $target_file;
        $this->task_count = 0;
    }
}

?>