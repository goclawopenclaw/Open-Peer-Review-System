import api from './api'

export const authService = {
  register: (name, email, password, affiliation) => {
    return api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: password,
      affiliation,
    })
  },

  login: (email, password) => {
    return api.post('/auth/login', { email, password })
  },

  logout: () => {
    return api.post('/auth/logout')
  },

  me: () => {
    return api.get('/auth/me')
  },

  refresh: () => {
    return api.post('/auth/refresh')
  },
}

export default authService
