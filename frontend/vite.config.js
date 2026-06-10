import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: true,
    proxy: {
      '/api': {
        target: 'http://nginx:80', // Wewnętrzny routing Dockera do backendu
        changeOrigin: true,
      }
    }
  }
})