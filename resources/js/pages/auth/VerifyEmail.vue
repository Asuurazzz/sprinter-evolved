<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  PinInput,
  PinInputGroup,
  PinInputSlot,
} from '@/components/ui/pin-input';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Mail } from 'lucide-vue-next';
import { ref } from 'vue';

const code = ref<string[]>([]);
const isResending = ref(false);

function handleComplete(value: string[]) {
  // TODO: Implementar verificação
  console.log('Código completo:', value.join(''));
}

function handleResendCode() {
  isResending.value = true;
  // TODO: Implementar reenvio
  setTimeout(() => {
    isResending.value = false;
    console.log('Código reenviado');
  }, 2000);
}

function handleSubmit() {
  // TODO: Implementar verificação
  console.log('Verificar código:', code.value.join(''));
}
</script>

<template>
  <AuthLayout title="Verifique seu e-mail" description="Enviamos um código de 6 dígitos para seu e-mail">
    <div class="space-y-6">
      <div class="flex justify-center">
        <div class="flex size-16 items-center justify-center rounded-full bg-primary/10">
          <Mail class="size-8 text-primary" />
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="flex justify-center">
          <PinInput
            v-model="code"
            placeholder=""
            @complete="handleComplete"
          >
            <PinInputGroup class="gap-2">
              <PinInputSlot
                v-for="(_, index) in 6"
                :key="index"
                :index="index"
                class="size-12 text-lg font-semibold"
              />
            </PinInputGroup>
          </PinInput>
        </div>

        <Button type="submit" class="w-full" :disabled="code.length < 6">
          Verificar
        </Button>
      </form>

      <div class="text-center">
        <p class="text-sm text-muted-foreground">
          Não recebeu o código?
        </p>
        <Button
          variant="link"
          class="h-auto p-0 text-primary"
          :disabled="isResending"
          @click="handleResendCode"
        >
          {{ isResending ? 'Reenviando...' : 'Reenviar código' }}
        </Button>
      </div>

      <p class="text-center text-sm text-muted-foreground">
        <RouterLink to="/login" class="text-primary hover:underline">
          Voltar para o login
        </RouterLink>
      </p>
    </div>
  </AuthLayout>
</template>
