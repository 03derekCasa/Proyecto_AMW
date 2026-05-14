import api from './api'

function normalizePostList(payload) {
    if (Array.isArray(payload?.data?.data)) {
        return payload.data.data
    }

    if (Array.isArray(payload?.data)) {
        return payload.data
    }

    if (Array.isArray(payload)) {
        return payload
    }

    return []
}

function normalizePost(payload) {
    return payload?.data || payload?.post || payload
}

function normalizeNextPageUrl(payload) {
    return payload?.data?.next_page_url || null
}

function normalizeApiUrl(url) {
    if (!url) {
        return '/posts'
    }

    return url.replace(/^https?:\/\/[^/]+\/api/, '')
}

export async function getPosts(url = '/posts') {
    const response = await api.get(normalizeApiUrl(url))
    const payload = response.data

    return {
        posts: normalizePostList(payload),
        nextPageUrl: normalizeNextPageUrl(payload),
        raw: payload,
    }
}

export async function getPost(postId) {
    const response = await api.get(`/posts/${postId}`)
    return normalizePost(response.data)
}

export async function getMyPosts() {
    const response = await api.get('/my-posts')
    const payload = response.data

    return {
        posts: normalizePostList(payload),
        raw: payload,
    }
}

export async function uploadPostImage(file) {
    const formData = new FormData()
    formData.append('image', file)

    const response = await api.post('/upload/image', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })

    const payload = response.data

    const imageUrl =
        payload?.data?.image_url ||
        payload?.data?.url ||
        payload?.image_url ||
        payload?.url

    if (!imageUrl) {
        throw new Error('No se recibió la URL de la imagen subida.')
    }

    return imageUrl
}

export async function createPost(postData) {
    const response = await api.post('/posts', postData)

    return response.data?.data ||
        response.data?.post ||
        response.data
}

export async function updatePost(postId, postData) {
    const response = await api.put(`/posts/${postId}`, postData)
    return normalizePost(response.data)
}

export async function deletePost(postId) {
    const response = await api.delete(`/posts/${postId}`)
    return response.data
}

export async function likePost(postId) {
    const response = await api.post(`/posts/${postId}/like`)
    return response.data
}

export async function unlikePost(postId) {
    const response = await api.delete(`/posts/${postId}/like`)
    return response.data
}

export async function getMyLikes() {
    const response = await api.get('/my-likes')
    const payload = response.data

    const items = normalizePostList(payload)

    const posts = items
        .map((item) => item.post || item)
        .filter((post) => post && post.id)

    return {
        posts,
        raw: payload,
    }
}

export async function createComment(postId, body) {
    const response = await api.post(`/posts/${postId}/comments`, {
        body,
    })

    return response.data?.data || response.data?.comment || response.data
}

export async function deleteComment(commentId) {
    const response = await api.delete(`/comments/${commentId}`)
    return response.data
}