<?php
namespace Models;


class User
{
    const ROLE_ADMIN = 'admin';
    const ROLE_DOCTOR = 'doctor';
    const ROLE_PATIENT = 'patient';

    protected ?int $id = null;
    protected string $username;
    protected string $email;
    protected string $password; 
    protected string $role;
    protected ?string $created_at = null;

    
    public function setPassword(string $password): void
    {
        if (empty(trim($password))) {
            throw new \InvalidArgumentException("Password cannot be empty");
        }
        
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

   
    public function setPasswordRaw(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

   
    public function getPassword(): string
    {
        return $this->password;
    }

   
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        if (empty(trim($username))) {
            throw new \InvalidArgumentException("Username cannot be empty");
        }
        $this->username = trim($username);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }
        $this->email = trim($email);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $validRoles = [self::ROLE_ADMIN, self::ROLE_DOCTOR, self::ROLE_PATIENT];
        if (!in_array($role, $validRoles)) {
            throw new \InvalidArgumentException("Invalid role");
        }
        $this->role = $role;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
}