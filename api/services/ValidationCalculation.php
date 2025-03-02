<?php
class validationCalculation {
    public function calculateGrade($midterm_score, $final_score){
        $final_grade = (0.4 * $midterm_score) + (0.6 * $final_score);
        return $final_grade;
    }
    public function calculateStatus($final_grade){
        $status = $final_grade >= 75 ? "PASSED" : "FAIL";
        return $status;
    }
    public function validateStudent($entity, string $request_type) {
        if($request_type == 'POST') {
            if (!isset($entity['student_name']) || empty(trim($entity['student_name']))) {
                throw new Exception("Student Name is required");
            }
        }
        if (!isset($entity['midterm_score']) || empty(trim($entity['midterm_score']))) {
                throw new Exception("Student Midterm Score is required");
        }
        if (!isset($entity['final_score']) || empty(trim($entity['final_score']))) {
                throw new Exception("Student Final Score is required");
        }
    }
}
?>