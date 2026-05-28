import api from './api'

export async function getNotifications() {
    const response = await api.get('/notifications')
    return response.data
}

export async function getNotificationSummary() {
    const response = await api.get('/notifications/summary')
    return response.data?.data || response.data
}

export async function markNotificationAsRead(notificationId) {
    const response = await api.patch(`/notifications/${notificationId}/read`)
    return response.data
}

export async function markAllNotificationsAsRead() {
    const response = await api.patch('/notifications/read-all')
    return response.data
}
