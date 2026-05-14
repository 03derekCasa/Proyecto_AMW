import api from './api'

export async function getMyPosts() {
    const response = await api.get('/my-posts')
    return response.data
}