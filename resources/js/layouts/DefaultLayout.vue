<script setup lang="ts">
import Footer from '@/components/common/Footer.vue';
import Header from '@/components/common/Header.vue';
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <Header />
    <main class="flex-1">
      <slot />
    </main>
    <Footer />
  </div>
</template>
