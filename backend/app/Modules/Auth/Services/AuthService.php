<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    /**
     * Register a new user with default 'author' role.
     */
    public function register(RegisterDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->users->create([
                'uuid' => Str::uuid()->toString(),
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
                'is_active' => true,
            ]);

            $user->assignRole('author');

            $token = JWTAuth::fromUser($user);
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();

            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('registered');

            return [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => $user->load('roles'),
                'permissions' => $permissions,
            ];
        });
    }

    /**
     * Authenticate user and issue JWT token.
     * Returns requires_2fa flag if 2FA is enabled.
     */
    public function login(LoginDTO $dto, ?string $ip = null): array
    {
        $user = $this->users->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        // Update login metadata
        $this->users->update($user, [
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);

        // Check 2FA (stub for Fase 3)
        if ($user->two_factor_enabled) {
            return [
                'requires_2fa' => true,
                'message' => 'Two-factor authentication required.',
            ];
        }

        $token = JWTAuth::fromUser($user);
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        event(new UserLoggedIn($user, $ip));

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user->load('roles'),
            'permissions' => $permissions,
        ];
    }

    /**
     * Invalidate the current JWT token.
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh the current JWT token.
     */
    public function refresh(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        $user = JWTAuth::setToken($token)->toUser();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user->load('roles'),
            'permissions' => $permissions,
        ];
    }

    /**
     * Get the currently authenticated user.
     */
    public function me(): User
    {
        return auth()->user()->load('roles');
    }
}
