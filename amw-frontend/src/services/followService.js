import api from './api'

export async function followUser(userId) {
    const response = await api.post(`/users/${userId}/follow`)
    return response.data?.data || response.data
}

export async function unfollowUser(userId) {
    const response = await api.delete(`/users/${userId}/follow`)
    return response.data?.data || response.data
}