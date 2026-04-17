import { useAuth } from 'src/composables/useAuth'

export default async () => {
  const { fetchMe } = useAuth()
  await fetchMe()
}
