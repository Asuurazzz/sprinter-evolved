import { computed, reactive, ref } from 'vue';

export interface ValidationRule {
    validate: (value: string) => boolean;
    message: string;
}

export interface FieldConfig {
    rules: ValidationRule[];
}

export interface FieldState {
    value: string;
    error: string;
    touched: boolean;
}

export function useFormValidation<T extends Record<string, FieldConfig>>(
    fieldsConfig: T,
) {
    type FieldNames = keyof T;

    const fields = reactive(
        Object.keys(fieldsConfig).reduce(
            (acc, key) => {
                acc[key as FieldNames] = {
                    value: '',
                    error: '',
                    touched: false,
                };
                return acc;
            },
            {} as Record<FieldNames, FieldState>,
        ),
    ) as Record<FieldNames, FieldState>;

    const isSubmitting = ref(false);

    function validateField(name: FieldNames): boolean {
        const field = fields[name];
        const config = fieldsConfig[name];

        for (const rule of config.rules) {
            if (!rule.validate(field.value)) {
                field.error = rule.message;
                return false;
            }
        }

        field.error = '';
        return true;
    }

    function touchField(name: FieldNames) {
        fields[name].touched = true;
        validateField(name);
    }

    function validateAll(): boolean {
        let isValid = true;

        for (const name of Object.keys(fieldsConfig) as FieldNames[]) {
            fields[name].touched = true;
            if (!validateField(name)) {
                isValid = false;
            }
        }

        return isValid;
    }

    function reset() {
        for (const name of Object.keys(fieldsConfig) as FieldNames[]) {
            const field = fields[name];
            field.value = '';
            field.error = '';
            field.touched = false;
        }
    }

    const isValid = computed(() => {
        for (const name of Object.keys(fieldsConfig) as FieldNames[]) {
            const field = fields[name];
            const config = fieldsConfig[name];

            for (const rule of config.rules) {
                if (!rule.validate(field.value)) {
                    return false;
                }
            }
        }
        return true;
    });

    return {
        fields,
        isSubmitting,
        isValid,
        validateField,
        touchField,
        validateAll,
        reset,
    };
}

// Regras de validação comuns
export const validationRules = {
    required: (message = 'Este campo é obrigatório'): ValidationRule => ({
        validate: (value) => value.trim().length > 0,
        message,
    }),

    email: (message = 'Digite um e-mail válido'): ValidationRule => ({
        validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        message,
    }),

    minLength: (
        length: number,
        message = `Mínimo de ${length} caracteres`,
    ): ValidationRule => ({
        validate: (value) => value.length >= length,
        message,
    }),

    hasUppercase: (
        message = 'Deve conter pelo menos uma letra maiúscula',
    ): ValidationRule => ({
        validate: (value) => /[A-Z]/.test(value),
        message,
    }),

    hasLowercase: (
        message = 'Deve conter pelo menos uma letra minúscula',
    ): ValidationRule => ({
        validate: (value) => /[a-z]/.test(value),
        message,
    }),

    hasSpecialChar: (
        message = 'Deve conter pelo menos um caractere especial',
    ): ValidationRule => ({
        validate: (value) => /[!@#$%^&*(),.?":{}|<>]/.test(value),
        message,
    }),

    matches: (
        getOtherValue: () => string,
        message = 'Os campos não coincidem',
    ): ValidationRule => ({
        validate: (value) => value === getOtherValue(),
        message,
    }),
};
