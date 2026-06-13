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
      },
      // DODAJEMY PROXY DLA PLIKÓW STATYCZNYCH Z BACKENDU
      '/uploads': {
        target: 'http://nginx:80',
        changeOrigin: true,
      },
      // NOWA REGUŁA DLA MERCURE
      '/.well-known/mercure': {
        target: 'http://mercure:80', // Wewnętrzna nazwa kontenera
        changeOrigin: true,
        // Mercure działa długotrwale, nie przerywamy połączeń SSE
        timeout: 0,
      }
    }
  }
})