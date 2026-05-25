import { useMutation } from '@tanstack/react-query'
import { useAuthStore } from '../stores/authStore'
import { authApi } from '../api/authApi'
import type { LoginPayload, AuthResponse } from '../types/auth.types'
import { useNavigate } from 'react-router-dom'
import { toast } from 'sonner'

export function useLogin() {
  const navigate = useNavigate()
  const setAuth = useAuthStore((s) => s.setAuth)

  return useMutation({
    mutationFn: (payload: LoginPayload) => authApi.login(payload),
    onSuccess: (data) => {
      // Check for 2FA requirement
      if ('requires_2fa' in data) {
        toast.info('Two-factor authentication required.')
        // TODO: Navigate to 2FA page in Fase 3
        return
      }

      const response = data as AuthResponse
      setAuth(response.data, response.access_token, response.permissions)
      toast.success(`Welcome back, ${response.data.name}!`)
      navigate('/dashboard')
    },
    onError: (error: { response?: { data?: { message?: string } } }) => {
      toast.error(error.response?.data?.message ?? 'Login failed. Please try again.')
    },
  })
}

export function useLogout() {
  const logout = useAuthStore((s) => s.logout)
  const navigate = useNavigate()

  return useMutation({
    mutationFn: () => authApi.logout(),
    onSuccess: () => {
      logout()
      toast.success('Logged out successfully.')
      navigate('/login')
    },
    onError: () => {
      // Force logout even if API call fails
      logout()
      navigate('/login')
    },
  })
}

export function useAuth() {
  const { user, isAuthenticated, permissions } = useAuthStore()

  const hasPermission = (permission: string | string[]) => {
    if (Array.isArray(permission)) {
      return permission.some((p) => permissions.includes(p))
    }
    return permissions.includes(permission)
  }

  return {
    user,
    isAuthenticated,
    permissions,
    hasPermission,
  }
}
