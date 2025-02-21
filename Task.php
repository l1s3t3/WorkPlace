<?php

class Task {
    public $id;
    public $description;
    public $estimate;
    public $completed;
    public $employeeId;
    public $status;

    public function __construct($id = null, $description = "", $estimate = null, $completed = "0", $employeeId = null) {
        $this->id = $id;
        $this->description = $description;
        $this->estimate = $estimate;
        $this->completed = $completed;
        $this->employeeId = $employeeId;
        $this->status = '';
    }
}

?>