import { useAuth } from '@/features/auth/hooks/useAuth'

export default function DashboardPage() {
  const { user } = useAuth()

  return (
    <div className="flex min-h-screen items-center justify-center bg-background p-4">
      <div className="text-center space-y-4">
        <h1 className="text-4xl font-bold tracking-tight">Dashboard</h1>
        <p className="text-muted-foreground">
          Welcome, <span className="font-semibold text-foreground">{user?.name ?? 'User'}</span>
        </p>
        <p className="text-sm text-muted-foreground">
          Dashboard layout will be built in Fase 2
        </p>
      </div>
    </div>
  )
}
