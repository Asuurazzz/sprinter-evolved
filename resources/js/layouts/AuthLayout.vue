<script setup lang="ts">
import Logo from '@/components/common/Logo.vue';
import { Zap, CheckCircle, LayoutGrid, Users } from 'lucide-vue-next';

defineProps<{
  title: string;
  description?: string;
}>();

const features = [
  { icon: LayoutGrid, text: 'Organize tarefas em quadros Kanban intuitivos' },
  { icon: Users, text: 'Colabore em tempo real com sua equipe' },
  { icon: Zap, text: 'Aumente sua produtividade em até 40%' },
];
</script>

<template>
  <div class="grid min-h-screen lg:grid-cols-2">
    <!-- Left side - Form -->
    <div class="flex flex-col">
      <div class="p-6">
        <RouterLink to="/" class="inline-block transition-opacity hover:opacity-80">
          <Logo size="sm" />
        </RouterLink>
      </div>

      <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md space-y-8">
          <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold tracking-tight">{{ title }}</h1>
            <p v-if="description" class="text-muted-foreground">{{ description }}</p>
          </div>

          <slot />
        </div>
      </div>
    </div>

    <!-- Right side - Decorative -->
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-primary via-primary/90 to-cyan-600 lg:block">
      <!-- Decorative circles -->
      <div class="absolute -right-20 -top-20 size-96 rounded-full bg-white/10" />
      <div class="absolute -bottom-32 -left-32 size-[500px] rounded-full bg-white/5" />
      <div class="absolute right-1/4 top-1/3 size-32 rounded-full bg-white/10" />

      <!-- Content -->
      <div class="relative flex h-full flex-col justify-center p-12">
        <div class="space-y-8">
          <div class="space-y-4">
            <h2 class="text-4xl font-bold text-white">
              Transforme a forma como você gerencia projetos
            </h2>
            <p class="text-lg text-white/80">
              Junte-se a milhares de equipes que usam o Sprinter para entregar projetos mais rápido.
            </p>
          </div>

          <div class="space-y-4">
            <div
              v-for="(feature, index) in features"
              :key="index"
              class="flex items-center gap-3 text-white/90"
            >
              <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/20">
                <CheckCircle class="size-4" />
              </div>
              <span>{{ feature.text }}</span>
            </div>
          </div>

          <div class="pt-8">
            <blockquote class="border-l-2 border-white/30 pl-4">
              <p class="text-lg italic text-white/90">
                "O Sprinter revolucionou a forma como nossa equipe trabalha. A produtividade aumentou drasticamente."
              </p>
              <footer class="mt-2 text-sm text-white/70">
                — Maria Silva, Tech Lead na StartupXYZ
              </footer>
            </blockquote>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
