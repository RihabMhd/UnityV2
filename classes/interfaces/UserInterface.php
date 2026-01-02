<?php

namespace Interfaces;
use Models\User;

interface UserInterface
{
    public function findById(int $id): ?User;

    public function findByUsername(string $username): ?User;

    public function findByEmail(string $email): ?User;

    public function findAll(): array;

    public function findByRole(string $role): array;

    public function create(User $user): bool;

    public function update(User $user): bool;

    public function delete(int $id): bool;

    public function usernameExists(string $username, ?int $excludeId = null): bool;

    public function emailExists(string $email, ?int $excludeId = null): bool;
}
