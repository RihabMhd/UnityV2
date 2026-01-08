<?php

namespace Repositories;

use Models\User;
use PDO;
use PDOException;

class UserRepository
{
    private $conn;
    private string $table_name = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?User
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByUsername(string $username): ?User
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByEmail(string $email): ?User
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapToEntity($row);
        }

        return $users;
    }

    public function findByRole(string $role): array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE role = :role ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapToEntity($row);
        }

        return $users;
    }

    public function create(User $user): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, role) 
                  VALUES (:username, :email, :password, :role)";

        $stmt = $this->conn->prepare($query);

        $username = $user->getUsername();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $role = $user->getRole();

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $user->setId((int)$this->conn->lastInsertId());
            return true;
        }

        return false;
    }

    public function update(User $user): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, 
                      email = :email, 
                      password = :password, 
                      role = :role 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $id = $user->getId();
        $username = $user->getUsername();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $role = $user->getRole();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function authenticate(string $username, string $password): ?User
    {
        $user = $this->findByUsername($username);
        
        if ($user && $user->verifyPassword($password)) {
            return $user;
        }

        return null;
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE username = :username";
        
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";
        
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    private function mapToEntity(array $row): User
    {
        $user = new User();
        $user->setId((int)$row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setPasswordRaw($row['password']);
        $user->setRole($row['role']);
        $user->setCreatedAt($row['created_at']);

        return $user;
    }
}