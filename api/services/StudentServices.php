<?php
require_once "repositories\StudentRepositories.php";

class StudentServices {
    private StudentRepositories $studentrepo;
    
    public function __construct($studentrepo){
        $this->studentrepo = $studentrepo;
    }
    public function getAllStud(): array {
        $student = [];

        foreach($this->studentrepo->getAllStud() as $stud){
            $student[] = $this::StudentFormat($stud,"GET");
        }
        return $student;
    }

    public function getStudById($id): ?Student{
        return $this::StudentFormat($this->studentrepo->getStudById($id),"GET");
    }

    public function addStudent($entity){
        $this::validateInput($entity, "POST");

        $student = $this::StudentFormat($entity,"POST");
        $this->studentrepo->addStud($student);
    }

    public function updateStudent($id,$entity){
        $result = $this::validateExistingStudent($id);
        $this::validateInput($entity,"PUT");
        $student = $this::StudentFormat($entity,"PUT");
        $this->studentrepo->updateStud($id,$student);
        return $result;
    }

    public function deleteStudent($id){
        $result = $this::validateExistingStudent($id);
        $this->studentrepo->deleteStud($id);
        return $result;
    }

    public function validateInput($entity, string $request_type) {
        if ($request_type == 'POST') {
            if (!isset($entity['student_name']) || trim($entity['student_name']) === "") {
                throw new Exception ("Student Name is required");
            }
        }

        if (!isset($entity['midterm_score']) || !is_numeric($entity['midterm_score']) || 
            ($entity['midterm_score'] < 0 || $entity['midterm_score'] > 100)) {
            throw new Exception ("Student Midterm Score is required and must be a number between 0 and 100.");
        }

        if (!isset($entity['final_score']) || !is_numeric($entity['final_score']) || 
            ($entity['final_score'] < 0 || $entity['final_score'] > 100)) {
            throw new Exception ("Student Final Score is required and must be a number between 0 and 100.");
        }
    }

    public function validateExistingStudent(int $id){
        $result = $this::getStudById($id);
        if(!$result){
            return "Student with id {$id} doesn't Exist!";
        }
    }
    public function StudentFormat($entity,string $request_type): ?Student{
        if($entity ==null){
            return null;
        }
        $student = new Student();
        $student->student_id = $entity['student_id'] ?? NULL;
        $student->student_name = $entity['student_name'] ?? NULL;
        $student->midterm_score = $entity['midterm_score'] ?? NULL;
        $student->final_score = $entity['final_score'] ?? NULL;
        $this::requestType($student,$entity,$request_type);
        return $student;

    }
    public function requestType($student,$entity,string $request_type){
        if($request_type == "POST" || $request_type == "PUT"){
            $student->final_grade = $this->calculateGrade($student->midterm_score,$student->final_score);
            $student->status = $this->calculateStatus($student->final_grade);
        }else{
            $student->final_grade = $entity['final_grade'] ?? NULL;
            $student->status = $entity['status'] ?? NULL;
        }
    }
    public function calculateGrade($midterm_score, $final_score){
        $final_grade = (0.4 * $midterm_score) + (0.6 * $final_score);
        return $final_grade;
    }
    public function calculateStatus($final_grade){
        $status = $final_grade >= 75 ? "PASSED" : "FAIL";
        return $status;
    }
}
?>