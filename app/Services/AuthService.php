<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Admin;

class AuthService
{
    protected UserRepositoryInterface $userRepository;
    protected AdminRepositoryInterface $adminRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AdminRepositoryInterface $adminRepository
    ) {
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
    }

    public function registerUser(array $data): array
    {
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        /** @var User $user */
        $user = $this->userRepository->create($data);
        $token = $user->createToken('user-auth-token', ['role:user'])->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function loginUser(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password credentials.'],
            ]);
        }

        $token = $user->createToken('user-auth-token', ['role:user'])->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function registerAdmin(array $data): array
    {
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        /** @var Admin $admin */
        $admin = $this->adminRepository->create($data);
        $token = $admin->createToken('admin-auth-token', ['role:admin'])->plainTextToken;

        return [
            'admin' => $admin,
            'token' => $token
        ];
    }

    public function loginAdmin(string $email, string $password): array
    {
        $admin = $this->adminRepository->findByEmail($email);

        if (!$admin || !Hash::check($password, $admin->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password credentials.'],
            ]);
        }

        $token = $admin->createToken('admin-auth-token', ['role:admin'])->plainTextToken;

        return [
            'admin' => $admin,
            'token' => $token
        ];
    }
}
