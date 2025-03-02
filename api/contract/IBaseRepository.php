<?php

interface IBaseRepository {
    public function getAllStud(): array;
    public function getStudById(int $id);
    public function addStud($entity);
    public function updateStud(int $id, $entity);
    public function deleteStud(int $id); 
}
?>