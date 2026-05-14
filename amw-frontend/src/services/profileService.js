import api from './api'

export async function getProfile() {
    const response = await api.get('/profile')
    return response.data
}

export async function updateProfile(profileData) {
    const response = await api.put('/profile', profileData)
    return response.data
}

export async function uploadProfileImage(file) {
    const formData = new FormData()
    formData.append('image', file)

    const response = await api.post('/profile/image', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })

    return response.data
}