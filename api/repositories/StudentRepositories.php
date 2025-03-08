<?php
require_once "config\Database.php";
require_once "model\Student.php";
require_once "contract\IBaseRepository.php";

class StudentRepositories implements IBaseRepository {
    private $databaseContext;
    private $table;

    public function __construct($database_context, $table) {
        $this->databaseContext = $database_context; 
        $this->table = $table;
    }

    public function getAllStud() : array {
            $query = "SELECT * FROM {$this->table}";
            $result = $this->executeQuery($query, []);
            return $this->BuildResultList($result);
    }

    public function getStudById(?int $id) : ?array {
            $query = "SELECT * FROM {$this->table} WHERE student_id = :id";
            $params = [':id' => $id];
            $result = $this->executeQuery($query,$params);
            return $this->BuildResult($result);
    }

    public function addStud($entity) {
        $query = "INSERT INTO {$this->table} (student_name, midterm_score, final_score, final_grade, status) VALUES (:name, :md_score, :final_score, :final_grade, :stat)";
        $params = $this::getParams($entity,null);
        $this->executeQuery($query, $params);
    }

    public function updateStud($id, $entity): void {
        $query = "UPDATE {$this->table} 
            SET midterm_score = :md_score, final_score = :final_score, 
            final_grade = :final_grade, status = :stat 
            WHERE student_id = :id";
        
        $params = $this::getParams($entity,$id);
        $this->executeQuery($query, $params);
    }

    public function deleteStud(int $id): void {
        $query = "DELETE FROM {$this->table} WHERE student_id = :id";
        $params = [':id' => $id];
        $this->executeQuery($query, $params);
    }

    public function BuildResult(?array $result) : ?array {
        if (!$result || empty($result[0])) {
            return null;
        }
        
        return $result[0];
    }

    public function BuildResultList(array $result) : array {
        $students = [];

        foreach ($result as $row) {
            $students[] = $row;
        }
        return $students;
    }
    public function getParams($student,$withID) : array {
        $params = [
            ':md_score' => $student->midterm_score ?? NULL,
            ':final_score' => $student->final_score ?? NULL,
            ':final_grade' => $student->final_grade ?? NULL,
            ':stat' => $student->status ?? NULL,
        ];
        
        if (!empty($student->student_name) || $student->student_name !==null) {
            $params[':name'] = $student->student_name;
        }
        if($withID !== null){
            $params[':id'] = $withID;
        }
        return $params;
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