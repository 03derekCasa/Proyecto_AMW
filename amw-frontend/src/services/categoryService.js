import api from './api'

export async function getCategories() {
    const response = await api.get('/categories')
    const payload = response.data

    if (Array.isArray(payload?.data)) {
        return payload.data
    }

    if (Array.isArray(payload)) {
        return payload
    }

    return []
}