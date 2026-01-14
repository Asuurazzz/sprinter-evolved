<script setup lang="ts">
import FormField from '@/components/common/FormField.vue';
import PasswordInput from '@/components/common/PasswordInput.vue';
import PasswordRequirements from '@/components/common/PasswordRequirements.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import {
  useFormValidation,
  validationRules,
} from '@/composables/useFormValidation';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { computed, ref, watch } from 'vue';

const passwordValue = ref('');

const { fields, isSubmitting, validateAll, touchField, isValid } = useFormValidation({
  name: {
    rules: [validationRules.required('Digite seu nome')],
  },
  email: {
    rules: [
      validationRules.required('Digite seu e-mail'),
      validationRules.email(),
    ],
  },
  password: {
    rules: [
      validationRules.required('Digite uma senha'),
      validationRules.minLength(8),
      validationRules.hasUppercase(),
      validationRules.hasLowercase(),
      validationRules.hasSpecialChar(),
    ],
  },
  passwordConfirmation: {
    rules: [
      validationRules.required('Confirme sua senha'),
      validationRules.matches(
        () => passwordValue.value,
        'As senhas não coincidem'
      ),
    ],
  },
});

watch(() => fields.password.value, (val) => {
  passwordValue.value = val;
});

const showPasswordRequirements = computed(
  () => fields.password.value.length > 0 || fields.password.touched
);

function handleGoogleLogin() {
  // TODO: Implementar registro com Google
  console.log('Registro com Google');
}

function handleSubmit() {
  if (!validateAll()) {
    return;
  }

  isSubmitting.value = true;

  // TODO: Implementar registro
  console.log('Register:', {
    name: fields.name.value,
    email: fields.email.value,
    password: fields.password.value,
  });

  setTimeout(() => {
    isSubmitting.value = false;
  }, 1000);
}
</script>

<template>
  <AuthLayout title="Crie sua conta" description="Comece a gerenciar seus projetos hoje">
    <form @submit.prevent="handleSubmit" class="space-y-6" novalidate>
      <div class="space-y-4">
        <FormField label="Nome" html-for="name" :error="fields.name.touched ? fields.name.error : ''">
          <Input id="name" v-model="fields.name.value" type="text" placeholder="Seu nome" autocomplete="name"
            :aria-invalid="fields.name.touched && !!fields.name.error" @blur="touchField('name')" />
        </FormField>

        <FormField label="E-mail" html-for="email" :error="fields.email.touched ? fields.email.error : ''">
          <Input id="email" v-model="fields.email.value" type="email" placeholder="seu@email.com" autocomplete="email"
            :aria-invalid="fields.email.touched && !!fields.email.error" @blur="touchField('email')" />
        </FormField>

        <FormField label="Senha" html-for="password" :error="fields.password.touched ? fields.password.error : ''">
          <PasswordInput id="password" v-model="fields.password.value" placeholder="••••••••"
            autocomplete="new-password" :error="fields.password.touched && !!fields.password.error"
            @blur="touchField('password')" />
          <PasswordRequirements v-if="showPasswordRequirements" :password="fields.password.value" class="mt-3" />
        </FormField>

        <FormField label="Confirmar senha" html-for="password-confirmation" :error="fields.passwordConfirmation.touched
          ? fields.passwordConfirmation.error
          : ''
          ">
          <PasswordInput id="password-confirmation" v-model="fields.passwordConfirmation.value" placeholder="••••••••"
            autocomplete="new-password" :error="fields.passwordConfirmation.touched &&
              !!fields.passwordConfirmation.error
              " @blur="touchField('passwordConfirmation')" />
        </FormField>
      </div>

      <Button type="submit" class="w-full" :disabled="isSubmitting || !isValid">
        {{ isSubmitting ? 'Criando conta...' : 'Criar conta' }}
      </Button>

      <div class="relative">
        <div class="absolute inset-0 flex items-center">
          <Separator class="w-full" />
        </div>
        <div class="relative flex justify-center text-xs uppercase">
          <span class="bg-background px-2 text-muted-foreground">ou continue com</span>
        </div>
      </div>

      <Button type="button" variant="outline" class="w-full gap-2" @click="handleGoogleLogin">
        <svg class="size-4" viewBox="0 0 24 24">
          <path fill="currentColor"
            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
          <path fill="currentColor"
            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
          <path fill="currentColor"
            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
          <path fill="currentColor"
            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
        </svg>
        Continuar com Google
      </Button>

      <p class="text-center text-sm text-muted-foreground">
        Já tem uma conta?
        <RouterLink to="/login" class="text-primary hover:underline">
          Entrar
        </RouterLink>
      </p>
    </form>
  </AuthLayout>
</template>
