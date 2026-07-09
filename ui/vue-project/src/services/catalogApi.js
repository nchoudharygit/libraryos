import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_CATALOG_API_URL || 'http://127.0.0.1:8001/api/v1',
  headers: { 'Content-Type': 'application/json' },
})

export const authorService = {
  getAll: () => api.get('/authors'),
  getOne: (id) => api.get(`/authors/${id}`),
  create: (data) => api.post('/authors', data),
  update: (id, data) => api.put(`/authors/${id}`, data),
  delete: (id) => api.delete(`/authors/${id}`),
}

export const bookService = {
  getAll: () => api.get('/books'),
  getOne: (id) => api.get(`/books/${id}`),
  create: (data) => api.post('/books', data),
  update: (id, data) => api.put(`/books/${id}`, data),
  delete: (id) => api.delete(`/books/${id}`),
}