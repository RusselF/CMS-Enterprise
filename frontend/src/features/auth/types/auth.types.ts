/**
 * Auth feature types.
 */

export interface User {
  id: string // uuid
  name: string
  email: string
  avatar: string | null
  two_factor_enabled: boolean
  email_verified_at: string | null
  last_login_at: string | null
  is_active: boolean
  roles: string[]
  created_at: string
  updated_at: string
}

export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  access_token: string
  token_type: string
  expires_in: number
  data: User
  permissions: string[]
}

export interface TwoFactorResponse {
  requires_2fa: true
  message: string
}
