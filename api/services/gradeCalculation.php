<?php
class gradeCalculation {
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