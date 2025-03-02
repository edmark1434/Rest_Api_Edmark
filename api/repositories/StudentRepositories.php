<?php
require_once "config\Database.php";
require_once "model\Student.php";
require_once "contract\IBaseRepository.php";
require_once "services\ValidationCalculation.php";

class StudentRepositories implements IBaseRepository {
    private $databaseContext;
    private $table;
    private $validationCalculation;

    public function __construct($database_context, $table) {
        $this->databaseContext = $database_context; 
        $this->table = $table;
        $this->validationCalculation = new ValidationCalculation();
    }

    public function getAllStud() : array {
            $query = "SELECT * FROM {$this->table}";
            $result = $this->executeQuery($query, []);
            return $this->BuildResultList($result);
    }

    public function getStudById(?int $id = null) : ?Student {
        if ($id) {
            $query = "SELECT * FROM {$this->table} WHERE student_id = :id";
            $result = $this->executeQuery($query, [':id' => $id]);
            $this->validationCalculation->validateExistingStudent($result,$id);
            return $this->BuildResult($result);
        }
        return null;
    }

    public function addStud($entity) {
        $this->validationCalculation->validateStudent($entity,'POST');

        $student_name = $entity['student_name'];
        $midterm_score = $entity['midterm_score'];
        $final_score = $entity['final_score'];

        $final_grade = $this->validationCalculation->calculateGrade($midterm_score, $final_score);
        $status = $this->validationCalculation->calculateStatus($final_grade);

        $student = new Student();
        $student->student_name = $student_name;
        $student->midterm_score = $midterm_score;
        $student->final_score = $final_score;
        $student->final_grade = $final_grade;
        $student->status = $status;

        $query = "INSERT INTO {$this->table} (student_name, midterm_score, final_score, final_grade, status) VALUES (:name, :md_score, :final_score, :final_grade, :stat)";
        $params = [
            ':name' => $student->student_name,
            ':md_score' => $student->midterm_score,
            ':final_score' => $student->final_score,
            ':final_grade' => $student->final_grade,
            ':stat' => $student->status
        ];

        $this->executeQuery($query, $params);

        return $student;
    }

    public function updateStud($id, $entity): void {
        $existingStudent = $this->getStudById($id);
        if(!$existingStudent){
            return;
        }
        $this->validationCalculation->validateStudent($entity,'PUT');

        $midterm_score = $entity['midterm_score'];
        $final_score = $entity['final_score'];

        $final_grade = $this->validationCalculation->calculateGrade($midterm_score, $final_score);
        $status = $this->validationCalculation->calculateStatus($final_grade);

        $query = "UPDATE {$this->table} 
            SET midterm_score = :md_score, final_score = :final_score, 
            final_grade = :final_grade, status = :stat 
            WHERE student_id = :id";
        
        $params = [
            ':id' => $id,
            ':md_score' => $midterm_score,
            ':final_score' => $final_score,
            ':final_grade' => $final_grade,
            ':stat' => $status
        ];

        $this->executeQuery($query, $params);
        echo json_encode(["success" => "Student with ID {$id} updated successfully"]);
    }

    public function deleteStud(int $id): void {
        $existingStudent = $this->getStudById($id);

        if (!$existingStudent) {
            echo json_encode(["error" => "Student with ID {$id} not found"]);
            return;
        }

        $query = "DELETE FROM {$this->table} WHERE student_id = :id";
        $params = [':id' => $id];
        $this->executeQuery($query, $params);
        echo json_encode(["success" => "Student with ID {$id} deleted successfully"]);
    }

    public function BuildResult(?array $result) : ?Student {
        if (!$result || empty($result[0])) {
            return null;
        }

        $row = $result[0];
        $stud = new Student();
        $stud->student_id = $row['student_id'];
        $stud->student_name = $row['student_name'];
        $stud->midterm_score = $row['midterm_score'];
        $stud->final_score = $row['final_score'];
        $stud->final_grade = $row['final_grade'];
        $stud->status = $row['status'];

        return $stud;
    }

    public function BuildResultList(array $result) : array {
        $students = [];

        foreach ($result as $row) {
            $student = new Student();
            $student->student_id = $row['student_id'];
            $student->student_name = $row['student_name'];
            $student->midterm_score = $row['midterm_score'];
            $student->final_score = $row['final_score'];
            $student->final_grade = $row['final_grade'];
            $student->status = $row['status'];
            $students[] = $student;
        }
        return $students;
    }

    public function executeQuery(string $query, array $params) {
        $statementObject = $this->databaseContext->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }
}
?>