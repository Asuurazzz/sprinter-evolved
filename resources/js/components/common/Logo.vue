<script setup lang="ts">
import { Zap } from 'lucide-vue-next';

defineProps<{
  size?: 'sm' | 'md' | 'lg';
  showText?: boolean;
}>();
</script>

<template>
  <div class="flex items-center gap-2">
    <div
      class="flex items-center justify-center rounded-lg bg-primary text-primary-foreground"
      :class="{
        'size-8': size === 'sm',
        'size-10': size === 'md' || !size,
        'size-12': size === 'lg',
      }"
    >
      <Zap
        :class="{
          'size-4': size === 'sm',
          'size-5': size === 'md' || !size,
          'size-6': size === 'lg',
        }"
        :stroke-width="2.5"
      />
    </div>
    <span
      v-if="showText !== false"
      class="font-bold tracking-tight text-foreground"
      :class="{
        'text-lg': size === 'sm',
        'text-xl': size === 'md' || !size,
        'text-2xl': size === 'lg',
      }"
    >
      Sprinter
    </span>
  </div>
</template>
