<script setup lang="ts">
import Logo from './Logo.vue';
</script>

<template>
  <footer class="border-t border-border bg-muted/30">
    <div class="container mx-auto px-4 py-12">
      <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
        <Logo size="sm" />

        <nav class="flex items-center gap-6">
          <a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">
            Termos de uso
          </a>
          <a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">
            Privacidade
          </a>
          <a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">
            Contato
          </a>
        </nav>
      </div>

      <div class="mt-8 border-t border-border pt-8 text-center">
        <p class="text-sm text-muted-foreground">
          © {{ new Date().getFullYear() }} Sprinter. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </footer>
</template>
