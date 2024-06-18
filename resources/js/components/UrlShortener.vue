<template>
    <div>

      <form @submit.prevent="shortenUrl">
        <input v-model="url" type="text" placeholder="Enter URL" required class="border-2 border-indigo-500/100 rounded-xl p-3 w-1/2">
        <button type="submit" class="ms-3 bg-indigo-500/100 hover:bg-indigo-700/100 text-white font-bold text-lg py-3 px-4 rounded-xl">Shorten</button>
      </form>

      <div v-if="shortUrl" class="mt-10">
        Short URL: <a :href="shortUrl" target="_blank" class="underline text-blue-600">{{ shortUrl }}</a>
      </div>

      <div v-if="error" class="mt-4 text-red-500">{{ error }}</div>

      <button v-if="shortUrl || error" @click="resetForm" class="mt-5 bg-gray-500 hover:bg-gray-700 text-white font-bold text-lg py-2 px-4 rounded-xl">
        Shorten Another URL
      </button>

    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        url: '',
        shortUrl: null,
        error: null
      };
    },
    methods: {
      async shortenUrl() {
        try {
          const response = await axios.post('/shorten', { url: this.url });
          this.shortUrl = response.data.short_url;
          this.error = null;
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
          this.error = error.response.data.errors.url ? error.response.data.errors.url[0] : 'An error occurred';
        } else {
          this.error = 'An error occurred';
        }
        this.shortUrl = null;
      }
      },
      resetForm() {
        this.url = '';
        this.shortUrl = null;
        this.error = null;
      }
    }
  };
  </script>
