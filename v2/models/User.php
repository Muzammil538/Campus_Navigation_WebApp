<?php
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $role;
    public $student_id;
    public $department;
    public $phone;
    public $profile_image;
    public $accessibility_mode;
    public $voice_navigation;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  SET full_name=:full_name, 
                      email=:email, 
                      password=:password, 
                      role=:role,
                      student_id=:student_id,
                      department=:department,
                      phone=:phone";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Bind
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":student_id", $this->student_id);
        $stmt->bindParam(":department", $this->department);
        $stmt->bindParam(":phone", $this->phone);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            $this->createDefaultSettings();
            return true;
        }
        return false;
    }

    // Login
    public function login() {
        $query = "SELECT id, full_name, email, password, role, accessibility_mode, voice_navigation 
                  FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->full_name = $row['full_name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                $this->accessibility_mode = $row['accessibility_mode'];
                $this->voice_navigation = $row['voice_navigation'];
                
                // Update last login
                $this->updateLastLogin();
                
                return true;
            }
        }
        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // Update profile
    public function updateProfile() {
        $query = "UPDATE " . $this->table . " 
                  SET full_name=:full_name,
                      phone=:phone,
                      department=:department,
                      student_id=:student_id
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":department", $this->department);
        $stmt->bindParam(":student_id", $this->student_id);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Update accessibility settings
    public function updateAccessibility($accessibility_mode, $voice_navigation) {
        $query = "UPDATE " . $this->table . " 
                  SET accessibility_mode=:accessibility_mode,
                      voice_navigation=:voice_navigation
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":accessibility_mode", $accessibility_mode);
        $stmt->bindParam(":voice_navigation", $voice_navigation);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Create default settings
    private function createDefaultSettings() {
        $query = "INSERT INTO user_settings (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->id);
        $stmt->execute();
    }

    // Update last login
    private function updateLastLogin() {
        $query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
    }

    // Check if email exists
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>
