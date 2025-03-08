<?php

require_once "repositories\StudentRepositories.php";
require_once "services\StudentServices.php";
require_once "config\Database.php";

class StudentController {
    private StudentRepositories $stud_repo;
    private StudentServices $student_services;

    public function __construct() {
        $database = new Database();
        $this->stud_repo = new StudentRepositories($database->getConnection(), "student");
        $this->student_services = new StudentServices($this->stud_repo);
    }

    public function getAllStud(): void {
        echo json_encode($this->student_services->getAllStud(),JSON_PRETTY_PRINT);
    }

    public function getStudById(int $id): void {
        $result = $this->student_services->getStudById($id);
        echo ($result !== null) ? json_encode($result,JSON_PRETTY_PRINT) : "Student with id {$id} doesn't Exist!";
    }

    public function addStud($student) {
        try{
            $this->student_services->addStudent($student);
            echo "Student Added Successfully";
        }catch(Exception $e){
            error_log("Request Handling Error: {$e->getMessage()}");
        }
        
    }

    public function updateStud(int $id, $student) {
        try{
            $result = $this->student_services->updateStudent($id, $student);
            echo ($result !== null) ? "{$result}":"Student Updated Successfully";
        }catch(Exception $e){
            error_log("Request Handling Error: {$e->getMessage()}");
        }
        
    }

    public function deleteStud(int $id) {
        try{
            $result = $this->student_services->deleteStudent($id);
            echo ($result !== null) ? "{$result}":"Student Deleted Successfully";
        }Catch(Exception $e){
            error_log("Request Handling Error: {$e->getMessage()}");
        }
    }
}
?>