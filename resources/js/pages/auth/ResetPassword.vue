<script setup lang="ts">
import FormField from '@/components/common/FormField.vue';
import PasswordInput from '@/components/common/PasswordInput.vue';
import PasswordRequirements from '@/components/common/PasswordRequirements.vue';
import { Button } from '@/components/ui/button';
import {
  PinInput,
  PinInputGroup,
  PinInputSlot,
} from '@/components/ui/pin-input';
import {
  useFormValidation,
  validationRules,
} from '@/composables/useFormValidation';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { ShieldCheck } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const code = ref<string[]>([]);
const isResending = ref(false);
const passwordValue = ref('');

const { fields, isSubmitting, validateAll, touchField } = useFormValidation({
  password: {
    rules: [
      validationRules.required('Digite uma nova senha'),
      validationRules.minLength(8),
      validationRules.hasUppercase(),
      validationRules.hasLowercase(),
      validationRules.hasSpecialChar(),
    ],
  },
  passwordConfirmation: {
    rules: [
      validationRules.required('Confirme sua nova senha'),
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

const isCodeComplete = computed(() => code.value.filter(Boolean).length === 6);

function handleComplete(value: string[]) {
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
  if (!isCodeComplete.value) {
    return;
  }

  if (!validateAll()) {
    return;
  }

  isSubmitting.value = true;

  // TODO: Implementar reset de senha
  console.log('Reset senha:', {
    code: code.value.join(''),
    password: fields.password.value,
  });

  setTimeout(() => {
    isSubmitting.value = false;
  }, 1000);
}
</script>

<template>
  <AuthLayout title="Redefinir senha" description="Digite o código enviado e sua nova senha">
    <div class="space-y-6">
      <div class="flex justify-center">
        <div class="flex size-16 items-center justify-center rounded-full bg-primary/10">
          <ShieldCheck class="size-8 text-primary" />
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6" novalidate>
        <div class="space-y-4">
          <div class="space-y-2">
            <p class="block text-center text-sm font-medium">
              Código de verificação
            </p>
            <div class="flex justify-center">
              <PinInput v-model="code" placeholder="" @complete="handleComplete">
                <PinInputGroup class="gap-2">
                  <PinInputSlot v-for="(_, index) in 6" :key="index" :index="index"
                    class="size-12 text-lg font-semibold" />
                </PinInputGroup>
              </PinInput>
            </div>
            <div class="text-center">
              <Button type="button" variant="link" class="h-auto p-0 text-sm text-primary" :disabled="isResending"
                @click="handleResendCode">
                {{ isResending ? 'Reenviando...' : 'Reenviar código' }}
              </Button>
            </div>
          </div>

          <FormField label="Nova senha" html-for="password"
            :error="fields.password.touched ? fields.password.error : ''">
            <PasswordInput id="password" v-model="fields.password.value" placeholder="••••••••"
              autocomplete="new-password" :error="fields.password.touched && !!fields.password.error"
              @blur="touchField('password')" />
            <PasswordRequirements v-if="showPasswordRequirements" :password="fields.password.value" class="mt-3" />
          </FormField>

          <FormField label="Confirmar nova senha" html-for="password-confirmation" :error="fields.passwordConfirmation.touched
            ? fields.passwordConfirmation.error
            : ''
            ">
            <PasswordInput id="password-confirmation" v-model="fields.passwordConfirmation.value" placeholder="••••••••"
              autocomplete="new-password" :error="fields.passwordConfirmation.touched &&
                !!fields.passwordConfirmation.error
                " @blur="touchField('passwordConfirmation')" />
          </FormField>
        </div>

        <Button type="submit" class="w-full" :disabled="!isCodeComplete || isSubmitting">
          {{ isSubmitting ? 'Redefinindo...' : 'Redefinir senha' }}
        </Button>
      </form>

      <p class="text-center text-sm text-muted-foreground">
        <RouterLink to="/login" class="text-primary hover:underline">
          Voltar para o login
        </RouterLink>
      </p>
    </div>
  </AuthLayout>
</template>
