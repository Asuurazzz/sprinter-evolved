<script setup lang="ts">
import { Label } from '@/components/ui/label';

defineProps<{
  label?: string;
  error?: string;
  htmlFor?: string;
}>();
</script>

<template>
  <div class="space-y-2">
    <div v-if="label || $slots.label" class="flex items-center justify-between">
      <Label v-if="label" :for="htmlFor">{{ label }}</Label>
      <slot name="label" />
    </div>
    <slot />
    <p v-if="error" class="text-xs text-destructive">
      {{ error }}
    </p>
  </div>
</template>
