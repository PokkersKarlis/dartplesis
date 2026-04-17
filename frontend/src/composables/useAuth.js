import { reactive, readonly } from 'vue'

const state = reactive({
  user: null,
  checked: false,
})

async function fetchMe() {
  try {
    const res = await fetch('/api/me', {
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    if (!res.ok) {
      state.user = null
      return
    }
    const data = await res.json().catch(() => ({}))
    state.user = data.user ?? null
  } catch {
    state.user = null
  } finally {
    state.checked = true
  }
}

async function login(email, password) {
  const res = await fetch('/api/login', {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify({ email, password }),
  })
  if (!res.ok) {
    const data = await res.json().catch(() => ({}))
    throw new Error(data.message ?? 'Login failed')
  }
  await fetchMe()
}

async function logout() {
  await fetch('/api/logout', { method: 'GET', credentials: 'include' }).catch(() => {})
  state.user = null
  state.checked = true
}

function isAdmin() {
  return state.user?.roles?.includes('ROLE_ADMINISTRATOR') ?? false
}

export function useAuth() {
  return { state: readonly(state), fetchMe, login, logout, isAdmin }
}
