<?php

namespace Models;

use InvalidArgumentException;

class User
{
    protected ?int $id = null;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected string $created_at;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_PATIENT = 'patient';

    private const ALLOWED_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DOCTOR,
        self::ROLE_PATIENT
    ];

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        if (empty(trim($username))) {
            throw new InvalidArgumentException("Le nom d'utilisateur est obligatoire.");
        }
        if (strlen($username) > 50) {
            throw new InvalidArgumentException("Le nom d'utilisateur ne doit pas dépasser 50 caractères.");
        }
        $this->username = trim($username);
    }

    public function setEmail(string $email): void
    {
        if (empty(trim($email))) {
            throw new InvalidArgumentException("L'email est obligatoire.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Format d'email invalide.");
        }
        $this->email = trim($email);
    }

    public function setPassword(string $password): void
    {
        if (empty($password)) {
            throw new InvalidArgumentException("Le mot de passe est obligatoire.");
        }
        if (strlen($password) < 6) {
            throw new InvalidArgumentException("Le mot de passe doit contenir au moins 6 caractères.");
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setPasswordRaw(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        if (!in_array($role, self::ALLOWED_ROLES)) {
            throw new InvalidArgumentException("Rôle invalide.");
        }
        $this->role = $role;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    public function __toString(): string
    {
        return sprintf(
            "User #%d | Username: %s | Email: %s | Role: %s",
            $this->id ?? 0,
            $this->username ?? 'N/A',
            $this->email ?? 'N/A',
            $this->role ?? 'N/A'
        );
    }
}