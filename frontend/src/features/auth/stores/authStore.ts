import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import type { User } from '../types/auth.types'

interface AuthState {
  user: User | null
  accessToken: string | null
  permissions: string[]
  isAuthenticated: boolean
  setAccessToken: (token: string) => void
  setUser: (user: User, permissions: string[]) => void
  setAuth: (user: User, token: string, permissions: string[]) => void
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      accessToken: null,
      permissions: [],
      isAuthenticated: false,

      setAccessToken: (token) =>
        set({ accessToken: token }),

      setUser: (user, permissions) =>
        set({ user, permissions, isAuthenticated: true }),

      setAuth: (user, token, permissions) =>
        set({
          user,
          accessToken: token,
          permissions,
          isAuthenticated: true,
        }),

      logout: () =>
        set({
          user: null,
          accessToken: null,
          permissions: [],
          isAuthenticated: false,
        }),
    }),
    {
      name: 'auth-storage',
      partialize: (state) => ({
        user: state.user,
        accessToken: state.accessToken,
        permissions: state.permissions,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
)
