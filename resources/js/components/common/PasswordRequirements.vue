<script setup lang="ts">
import { Check, X } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
  password: string;
}>();

const requirements = computed(() => [
  {
    label: 'Mínimo de 8 caracteres',
    valid: props.password.length >= 8,
  },
  {
    label: 'Uma letra maiúscula',
    valid: /[A-Z]/.test(props.password),
  },
  {
    label: 'Uma letra minúscula',
    valid: /[a-z]/.test(props.password),
  },
  {
    label: 'Um caractere especial',
    valid: /[!@#$%^&*(),.?":{}|<>]/.test(props.password),
  },
]);

const allValid = computed(() => requirements.value.every((r) => r.valid));

defineExpose({ allValid });
</script>

<template>
  <div class="space-y-2">
    <p class="text-xs font-medium text-muted-foreground">Requisitos da senha:</p>
    <ul class="space-y-1">
      <li v-for="req in requirements" :key="req.label" class="flex items-center gap-2 text-xs"
        :class="req.valid ? 'text-green-600 dark:text-green-400' : 'text-muted-foreground'">
        <Check v-if="req.valid" class="size-3.5" />
        <X v-else class="size-3.5" />
        <span>{{ req.label }}</span>
      </li>
    </ul>
  </div>
</template>
