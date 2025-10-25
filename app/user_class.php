<?php
   class User {
       private $pdo;

       public function __construct($pdo) {
           $this->pdo = $pdo;
       }

       // Ví dụ: Lấy thông tin người dùng theo ID
       public function getUserById($id) {
           $query = "SELECT * FROM users WHERE id = ?";
           $stmt = $this->pdo->prepare($query);
           $stmt->execute([$id]);
           return $stmt->fetch(PDO::FETCH_ASSOC);
       }
   }
   ?>