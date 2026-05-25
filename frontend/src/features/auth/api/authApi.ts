import { api } from '@/shared/lib/axios'
import type {
  LoginPayload,
  RegisterPayload,
  AuthResponse,
  TwoFactorResponse,
  User,
} from '../types/auth.types'

export const authApi = {
  login: (payload: LoginPayload) =>
    api.post<AuthResponse | TwoFactorResponse>('/auth/login', payload)
      .then((r) => r.data),

  register: (payload: RegisterPayload) =>
    api.post<AuthResponse>('/auth/register', payload)
      .then((r) => r.data),

  logout: () =>
    api.post('/auth/logout'),

  refresh: () =>
    api.post<AuthResponse>('/auth/refresh')
      .then((r) => r.data),

  me: () =>
    api.get<{ data: User; permissions: string[] }>('/auth/me')
      .then((r) => r.data),
}
