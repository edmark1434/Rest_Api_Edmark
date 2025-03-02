<?php

require_once "repositories\StudentRepositories.php";
require_once "config\Database.php";

class StudentController {
    private StudentRepositories $stud_repo;

    public function __construct() {
        $database = new Database();
        $this->stud_repo = new StudentRepositories($database->getConnection(), "student");
    }

    public function getAllStud(): void {
        echo json_encode($this->stud_repo->getAllStud(),JSON_PRETTY_PRINT);
    }

    public function getStudById(int $id): void {
        echo json_encode($this->stud_repo->getStudById($id),JSON_PRETTY_PRINT);
    }

    public function addStud($student) {
        $this->stud_repo->addStud($student);
        echo "Student Added Successfully";
    }

    public function updateStud(int $id, $student) {
        $this->stud_repo->updateStud($id, $student);
    }

    public function deleteStud(int $id) {
        $this->stud_repo->deleteStud($id);
    }
}
?>