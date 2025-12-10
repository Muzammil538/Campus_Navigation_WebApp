<?php
class Building {
    private $conn;
    private $table = 'buildings';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all buildings
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get building by ID
    public function getById($id) {
        $query = "SELECT b.*, 
                  (SELECT COUNT(*) FROM bookmarks WHERE building_id = b.id) as bookmark_count
                  FROM " . $this->table . " b 
                  WHERE b.id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get buildings by category
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE category = :category 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search buildings
    public function search($searchTerm) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE name LIKE :search 
                  OR code LIKE :search 
                  OR description LIKE :search 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(":search", $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get facilities for a building
    public function getFacilities($building_id) {
        $query = "SELECT * FROM facilities 
                  WHERE building_id = :building_id 
                  ORDER BY floor ASC, name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":building_id", $building_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get nearby buildings
    public function getNearby($latitude, $longitude, $radius = 1000) {
        // Using Haversine formula
        $query = "SELECT *, 
                  (6371000 * acos(cos(radians(:lat)) * cos(radians(latitude)) * 
                  cos(radians(longitude) - radians(:lng)) + 
                  sin(radians(:lat)) * sin(radians(latitude)))) AS distance 
                  FROM " . $this->table . " 
                  HAVING distance < :radius 
                  ORDER BY distance ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":lat", $latitude);
        $stmt->bindParam(":lng", $longitude);
        $stmt->bindParam(":radius", $radius);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get categories with counts
    public function getCategoriesWithCount() {
        $query = "SELECT category, COUNT(*) as count 
                  FROM " . $this->table . " 
                  GROUP BY category 
                  ORDER BY category ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
