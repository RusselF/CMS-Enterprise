import axios from 'axios'
import { useAuthStore } from '@/features/auth/stores/authStore'

/**
 * Axios instance configured for the Enterprise CMS API.
 * Includes JWT token injection and auto-refresh on 401.
 */
export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL + '/api/v1',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

// ─── Request Interceptor: Inject JWT token ─────────────

api.interceptors.request.use((config) => {
  const token = useAuthStore.getState().accessToken
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// ─── Response Interceptor: Auto-refresh on 401 ────────

let isRefreshing = false
let failedQueue: Array<{
  resolve: (value: string) => void
  reject: (reason: unknown) => void
}> = []

const processQueue = (error: unknown, token: string | null = null) => {
  failedQueue.forEach(({ resolve, reject }) => {
    if (token) resolve(token)
    else reject(error)
  })
  failedQueue = []
}

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const original = error.config

    // Skip refresh for non-401 errors or already-retried requests
    if (error.response?.status !== 401 || original._retry) {
      return Promise.reject(error)
    }

    // Skip refresh for auth endpoints themselves
    if (original.url?.includes('/auth/login') || original.url?.includes('/auth/refresh')) {
      return Promise.reject(error)
    }

    if (isRefreshing) {
      return new Promise<string>((resolve, reject) => {
        failedQueue.push({ resolve, reject })
      }).then((token) => {
        original.headers.Authorization = `Bearer ${token}`
        return api(original)
      })
    }

    original._retry = true
    isRefreshing = true

    try {
      const { data } = await axios.post(
        import.meta.env.VITE_API_URL + '/api/v1/auth/refresh',
        {},
        { withCredentials: true }
      )
      const newToken = data.access_token

      useAuthStore.getState().setAccessToken(newToken)
      processQueue(null, newToken)

      original.headers.Authorization = `Bearer ${newToken}`
      return api(original)
    } catch (refreshError) {
      processQueue(refreshError, null)

      // Force logout on refresh failure
      useAuthStore.getState().logout()
      window.location.href = '/login'

      return Promise.reject(refreshError)
    } finally {
      isRefreshing = false
    }
  }
)
