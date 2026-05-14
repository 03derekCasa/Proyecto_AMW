import api from './api'

export async function loginUser(credentials) {
    const response = await api.post('/login', credentials)

    localStorage.setItem('amw_token', response.data.token)
    localStorage.setItem('amw_user', JSON.stringify(response.data.user))

    return response.data
}

export async function registerUser(data) {
    const response = await api.post('/register', data)

    // En el registro NO guardamos token.
    // Así obligamos al usuario a ir después al login.
    return response.data
}

export async function logoutUser() {
    await api.post('/logout')

    localStorage.removeItem('amw_token')
    localStorage.removeItem('amw_user')
}