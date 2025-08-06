<?php
/**
 * Authentication System for EduForge
 * includes/auth.php
 */

session_start();
require_once 'db.php';

class Auth {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Register a new user
    public function register($name, $email, $password, $role = 'student') {
        // Check if email already exists
        $stmt = executeQuery($this->db, "SELECT id FROM users WHERE email = ?", [$email]);
        if ($stmt && $stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = executeQuery($this->db, 
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)",
            [$name, $email, $hashedPassword, $role]
        );

        if ($stmt) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    // Login user
    public function login($email, $password) {
        $stmt = executeQuery($this->db, "SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($stmt && $stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid password'];
            }
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
    }

    // Logout user
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Get current user info
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }

    // Check user role
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    // Require login (redirect if not logged in)
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            redirect('login.php');
        }
    }

    // Require specific role
    public function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            redirect('dashboard.php');
        }
    }
}

// Initialize auth system
$auth = new Auth($db);

// Helper functions
function isLoggedIn() {
    global $auth;
    return $auth->isLoggedIn();
}

function getCurrentUser() {
    global $auth;
    return $auth->getCurrentUser();
}

function hasRole($role) {
    global $auth;
    return $auth->hasRole($role);
}

function requireLogin() {
    global $auth;
    $auth->requireLogin();
}

function requireRole($role) {
    global $auth;
    $auth->requireRole($role);
}
?>