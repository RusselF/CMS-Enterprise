<?php

namespace App\Modules\Auth\Services;

/**
 * Two-factor authentication service.
 * Stub for Fase 3 — TOTP setup, verify, backup codes.
 */
class TwoFactorService
{
    public function generateSecret(): string
    {
        // TODO: Implement with TOTP library in Fase 3
        return '';
    }

    public function verify(string $secret, string $code): bool
    {
        // TODO: Implement TOTP verification in Fase 3
        return false;
    }
}
