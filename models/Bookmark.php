<?php
class Bookmark {
    private $conn;
    private $table = 'bookmarks';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add bookmark
    public function add($user_id, $building_id) {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, building_id=:building_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":building_id", $building_id);
        
        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            // Duplicate entry
            return false;
        }
    }

    // Remove bookmark
    public function remove($user_id, $building_id) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id=:user_id AND building_id=:building_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":building_id", $building_id);
        
        return $stmt->execute();
    }

    // Get user bookmarks
    public function getUserBookmarks($user_id) {
        $query = "SELECT b.*, bm.created_at as bookmarked_at 
                  FROM buildings b 
                  INNER JOIN " . $this->table . " bm ON b.id = bm.building_id 
                  WHERE bm.user_id = :user_id 
                  ORDER BY bm.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if bookmarked
    public function isBookmarked($user_id, $building_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE user_id=:user_id AND building_id=:building_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":building_id", $building_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>
