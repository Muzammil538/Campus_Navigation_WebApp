<?php
class Department {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function all() {
        $stmt = $this->db->query("SELECT * FROM departments ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function search($query) {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE name LIKE :q OR code LIKE :q ORDER BY name");
        $stmt->execute([':q' => "%{$query}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
