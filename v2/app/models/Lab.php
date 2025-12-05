<?php
class Lab {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function findWithDept($id) {
        $stmt = $this->db->prepare("
            SELECT l.*, d.name as dept_name, d.code as dept_code, d.id as dept_id 
            FROM labs l JOIN departments d ON d.id = l.department_id 
            WHERE l.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByDept($deptId) {
        $stmt = $this->db->prepare("SELECT * FROM labs WHERE department_id = :dept_id ORDER BY name");
        $stmt->execute([':dept_id' => $deptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
