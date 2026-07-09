<template>
  <div class="min-h-screen bg-gray-50">
    
    <!-- Loading state -->
    <div v-if="isLoading" class="flex justify-center mt-20">
      <p>Loading...</p>
    </div>

    <div v-else>
      <!-- Top nav -->
      <nav class="bg-white border-b px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-xl font-bold text-blue-700">📚 LibraryOS</span>
        </div>
        <div class="flex gap-6 text-sm font-medium items-center">
          <RouterLink to="/catalog" class="text-gray-600 hover:text-blue-600">Catalog</RouterLink>
          <RouterLink to="/inventory" class="text-gray-400 cursor-not-allowed">Inventory</RouterLink>
          <RouterLink to="/members" class="text-gray-400 cursor-not-allowed">Members</RouterLink>
          <RouterLink to="/analytics" class="text-gray-400 cursor-not-allowed">Analytics</RouterLink>
          
          <!-- Auth buttons -->
          <div v-if="isAuthenticated && user">
            <span class="text-gray-600 mr-4">{{ user.email }}</span>
            <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
              Logout
            </button>
          </div>
          <div v-else>
            <p v-if="error" class="text-red-500 text-xs mr-2">{{ error.message }}</p>
            <button @click="signup" class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">
              Signup
            </button>
            <button @click="login" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
              Login
            </button>
          </div>
        </div>
      </nav>

      <!-- Main content — sirf logged in hone pe dikhega -->
      <div v-if="isAuthenticated">
        <main class="max-w-6xl mx-auto mt-6">
          <RouterView />
        </main>
      </div>

      <!-- Login prompt -->
      <div v-else class="flex justify-center mt-20">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-700 mb-4">Welcome to LibraryOS 📚</h2>
          <p class="text-gray-500 mb-6">Please login to access the dashboard</p>
          <button @click="login" class="bg-blue-600 text-white px-6 py-2 rounded mr-3">
            Login
          </button>
          <button @click="signup" class="bg-green-600 text-white px-6 py-2 rounded">
            Signup
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth0 } from '@auth0/auth0-vue'

const {
  isLoading,
  isAuthenticated,
  error,
  loginWithRedirect,
  logout: auth0Logout,
  user
} = useAuth0()

const signup = () =>
  loginWithRedirect({ authorizationParams: { screen_hint: 'signup' } })

const login = () => loginWithRedirect()

const logout = () =>
  auth0Logout({ logoutParams: { returnTo: window.location.origin } })
</script>