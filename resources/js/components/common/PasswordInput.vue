<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Eye, EyeOff } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';
import { ref } from 'vue';

const props = defineProps<{
  modelValue?: string;
  placeholder?: string;
  id?: string;
  class?: HTMLAttributes['class'];
  error?: boolean;
  autocomplete?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
  (e: 'blur'): void;
}>();

const showPassword = ref(false);

function toggleVisibility() {
  showPassword.value = !showPassword.value;
}
</script>

<template>
  <div class="relative">
    <input :id="id" :type="showPassword ? 'text' : 'password'" :value="modelValue" :placeholder="placeholder"
      :autocomplete="autocomplete" :aria-invalid="error" :class="cn(
        'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 pr-10 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        props.class,
      )" @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)" @blur="emit('blur')" />
    <button type="button" tabindex="-1"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
      @click="toggleVisibility">
      <Eye v-if="!showPassword" class="size-4" />
      <EyeOff v-else class="size-4" />
    </button>
  </div>
</template>
