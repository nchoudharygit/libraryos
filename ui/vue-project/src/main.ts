import './assets/main.css'

// @ts-ignore: Vue SFC type declarations are handled via shims or build tooling
import App from './App.vue'

import { createApp } from 'vue'
import { createAuth0 } from '@auth0/auth0-vue'

// @ts-ignore
import router from './router'

const app = createApp(App)

app.use(router)
app.use(
    createAuth0({
        domain: import.meta.env.VITE_AUTH0_DOMAIN,
        clientId: import.meta.env.VITE_AUTH0_CLIENT_ID,
        authorizationParams: {
            redirect_uri: window.location.origin
        }
    })
)

app.mount('#app')