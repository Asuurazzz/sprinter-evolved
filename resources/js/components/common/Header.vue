<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Logo from './Logo.vue';
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b border-border/50 bg-background/80 backdrop-blur-sm">
    <div class="container mx-auto flex h-16 items-center justify-between px-4">
      <RouterLink to="/" class="transition-opacity hover:opacity-80">
        <Logo />
      </RouterLink>

      <nav class="hidden items-center gap-6 md:flex">
        <a href="#features" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
          Recursos
        </a>
        <a href="#pricing" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
          Preços
        </a>
      </nav>

      <div class="flex items-center gap-3">
        <Button variant="ghost" as-child>
          <RouterLink to="/login">Entrar</RouterLink>
        </Button>
        <Button as-child>
          <RouterLink to="/register">Começar grátis</RouterLink>
        </Button>
      </div>
    </div>
  </header>
</template>
