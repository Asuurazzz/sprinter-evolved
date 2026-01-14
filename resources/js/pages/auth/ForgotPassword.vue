<script setup lang="ts">
import FormField from '@/components/common/FormField.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  useFormValidation,
  validationRules,
} from '@/composables/useFormValidation';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { KeyRound } from 'lucide-vue-next';

const { fields, isSubmitting, validateAll, touchField, isValid } = useFormValidation({
  email: {
    rules: [
      validationRules.required('Digite seu e-mail'),
      validationRules.email(),
    ],
  },
});

function handleSubmit() {
  if (!validateAll()) {
    return;
  }

  isSubmitting.value = true;

  // TODO: Implementar recuperação de senha
  console.log('Recuperar senha para:', fields.email.value);

  setTimeout(() => {
    isSubmitting.value = false;
  }, 1000);
}
</script>

<template>
  <AuthLayout title="Esqueceu sua senha?" description="Digite seu e-mail para receber o código de recuperação">
    <div class="space-y-6">
      <div class="flex justify-center">
        <div class="flex size-16 items-center justify-center rounded-full bg-primary/10">
          <KeyRound class="size-8 text-primary" />
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6" novalidate>
        <FormField label="E-mail" html-for="email" :error="fields.email.touched ? fields.email.error : ''">
          <Input id="email" v-model="fields.email.value" type="email" placeholder="seu@email.com" autocomplete="email"
            :aria-invalid="fields.email.touched && !!fields.email.error" @blur="touchField('email')" />
        </FormField>

        <Button type="submit" class="w-full" :disabled="isSubmitting || !isValid">
          {{ isSubmitting ? 'Enviando...' : 'Enviar código' }}
        </Button>
      </form>

      <p class="text-center text-sm text-muted-foreground">
        Lembrou sua senha?
        <RouterLink to="/login" class="text-primary hover:underline">
          Voltar para o login
        </RouterLink>
      </p>
    </div>
  </AuthLayout>
</template>
