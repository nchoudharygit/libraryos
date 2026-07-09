<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Catalog</h1>

    <!-- Sub tabs -->
    <div class="flex gap-4 mb-6 border-b">
      <button
        @click="activeTab = 'books'"
        :class="activeTab === 'books' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
        class="pb-2 font-medium"
      >Books</button>
      <button
        @click="activeTab = 'authors'"
        :class="activeTab === 'authors' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
        class="pb-2 font-medium"
      >Authors</button>
    </div>

    <!-- Books Tab -->
    <div v-if="activeTab === 'books'">
      <div class="flex justify-between mb-4">
        <h2 class="text-lg font-medium">All Books</h2>
      </div>
      <div v-if="loading" class="text-gray-400">Loading...</div>
      <table v-else class="w-full text-sm border-collapse">
        <thead>
          <tr class="bg-gray-50 text-left">
            <th class="px-4 py-3 border-b">Title</th>
            <th class="px-4 py-3 border-b">Author</th>
            <th class="px-4 py-3 border-b">Genre</th>
            <th class="px-4 py-3 border-b">ISBN</th>
            <th class="px-4 py-3 border-b">Copies</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books" :key="book.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 border-b">{{ book.title }}</td>
            <td class="px-4 py-3 border-b">{{ book.author?.name }}</td>
            <td class="px-4 py-3 border-b">{{ book.genre }}</td>
            <td class="px-4 py-3 border-b font-mono text-xs">{{ book.isbn }}</td>
            <td class="px-4 py-3 border-b">{{ book.total_copies }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Authors Tab -->
    <div v-if="activeTab === 'authors'">
      <div class="flex justify-between mb-4">
        <h2 class="text-lg font-medium">All Authors</h2>
      </div>
      <div v-if="loading" class="text-gray-400">Loading...</div>
      <table v-else class="w-full text-sm border-collapse">
        <thead>
          <tr class="bg-gray-50 text-left">
            <th class="px-4 py-3 border-b">Name</th>
            <th class="px-4 py-3 border-b">Nationality</th>
            <th class="px-4 py-3 border-b">Email</th>
            <th class="px-4 py-3 border-b">Books</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="author in authors" :key="author.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 border-b font-medium">{{ author.name }}</td>
            <td class="px-4 py-3 border-b">{{ author.nationality }}</td>
            <td class="px-4 py-3 border-b">{{ author.email }}</td>
            <td class="px-4 py-3 border-b">{{ author.books?.length || 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { bookService, authorService } from '@/services/catalogApi'

const activeTab = ref('books')
const books = ref([])
const authors = ref([])
const loading = ref(false)

async function loadBooks() {
  loading.value = true
  const res = await bookService.getAll()
  books.value = res.data.data
  loading.value = false
}

async function loadAuthors() {
  loading.value = true
  const res = await authorService.getAll()
  authors.value = res.data.data
  loading.value = false
}

watch(activeTab, (tab) => {
  if (tab === 'books') loadBooks()
  if (tab === 'authors') loadAuthors()
})

onMounted(() => loadBooks())
</script>