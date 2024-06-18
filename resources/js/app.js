import './bootstrap';

import { createApp } from 'vue'

const app = createApp()

import UrlShortener from './components/UrlShortener.vue';
app.component('url-shortener', UrlShortener);

app.mount('#app')
