<script setup lang="ts">
import { Button } from '@/components/ui/button';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import {
  LayoutGrid,
  Users,
  Zap,
  Clock,
  ArrowRight,
  CheckCircle,
} from 'lucide-vue-next';

const features = [
  {
    icon: LayoutGrid,
    title: 'Quadros Kanban',
    description:
      'Visualize seu fluxo de trabalho com quadros personalizáveis. Arraste e solte tarefas entre colunas.',
  },
  {
    icon: Users,
    title: 'Colaboração em equipe',
    description:
      'Trabalhe em conjunto com sua equipe em tempo real. Comentários, menções e notificações.',
  },
  {
    icon: Zap,
    title: 'Automações',
    description:
      'Automatize tarefas repetitivas e economize tempo. Regras personalizadas para seu workflow.',
  },
  {
    icon: Clock,
    title: 'Prazos e lembretes',
    description:
      'Nunca perca um prazo. Defina datas de entrega e receba lembretes automáticos.',
  },
];
</script>

<template>
  <DefaultLayout>
    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 lg:py-32">
      <!-- Background decoration -->
      <div class="absolute inset-0 -z-10">
        <div class="absolute left-1/2 top-0 -translate-x-1/2 blur-3xl">
          <div class="aspect-square w-[600px] rounded-full bg-primary/20" />
        </div>
      </div>

      <div class="container mx-auto px-4">
        <div class="mx-auto max-w-3xl text-center">
          <div
            class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm font-medium text-primary"
          >
            <Zap class="size-4" />
            <span>Gerenciamento de projetos simplificado</span>
          </div>

          <h1 class="mb-6 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Organize projetos com
            <span class="text-primary">velocidade</span>
            e clareza
          </h1>

          <p class="mb-10 text-lg text-muted-foreground lg:text-xl">
            O Sprinter é a ferramenta Kanban que sua equipe precisa para entregar projetos mais rápido.
            Simples, intuitivo e poderoso.
          </p>

          <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
            <Button size="lg" as-child class="gap-2">
              <RouterLink to="/register">
                Começar grátis
                <ArrowRight class="size-4" />
              </RouterLink>
            </Button>
            <Button size="lg" variant="outline" as-child>
              <a href="#features">Ver recursos</a>
            </Button>
          </div>

          <div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-sm text-muted-foreground">
            <div class="flex items-center gap-2">
              <CheckCircle class="size-4 text-primary" />
              <span>Grátis para começar</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle class="size-4 text-primary" />
              <span>Sem cartão de crédito</span>
            </div>
            <div class="flex items-center gap-2">
              <CheckCircle class="size-4 text-primary" />
              <span>Cancele quando quiser</span>
            </div>
          </div>
        </div>

        <!-- Mock Kanban Board -->
        <div class="relative mx-auto mt-16 max-w-5xl lg:mt-24">
          <div class="rounded-xl border border-border bg-card p-4 shadow-2xl shadow-primary/10">
            <div class="grid grid-cols-3 gap-4">
              <!-- Column 1 -->
              <div class="rounded-lg bg-muted/50 p-3">
                <div class="mb-3 flex items-center justify-between">
                  <span class="text-sm font-medium">A fazer</span>
                  <span class="rounded bg-muted px-2 py-0.5 text-xs text-muted-foreground">3</span>
                </div>
                <div class="space-y-2">
                  <div class="rounded-md border border-border bg-card p-3 shadow-sm">
                    <p class="text-sm font-medium">Criar wireframes</p>
                    <p class="mt-1 text-xs text-muted-foreground">Design • Alta</p>
                  </div>
                  <div class="rounded-md border border-border bg-card p-3 shadow-sm">
                    <p class="text-sm font-medium">Revisar documentação</p>
                    <p class="mt-1 text-xs text-muted-foreground">Docs • Média</p>
                  </div>
                  <div class="rounded-md border border-border bg-card p-3 shadow-sm">
                    <p class="text-sm font-medium">Setup do projeto</p>
                    <p class="mt-1 text-xs text-muted-foreground">Dev • Baixa</p>
                  </div>
                </div>
              </div>

              <!-- Column 2 -->
              <div class="rounded-lg bg-muted/50 p-3">
                <div class="mb-3 flex items-center justify-between">
                  <span class="text-sm font-medium">Em progresso</span>
                  <span class="rounded bg-primary/10 px-2 py-0.5 text-xs text-primary">2</span>
                </div>
                <div class="space-y-2">
                  <div class="rounded-md border border-primary/30 bg-card p-3 shadow-sm ring-1 ring-primary/20">
                    <p class="text-sm font-medium">Implementar auth</p>
                    <p class="mt-1 text-xs text-muted-foreground">Dev • Alta</p>
                  </div>
                  <div class="rounded-md border border-border bg-card p-3 shadow-sm">
                    <p class="text-sm font-medium">Design do dashboard</p>
                    <p class="mt-1 text-xs text-muted-foreground">Design • Alta</p>
                  </div>
                </div>
              </div>

              <!-- Column 3 -->
              <div class="rounded-lg bg-muted/50 p-3">
                <div class="mb-3 flex items-center justify-between">
                  <span class="text-sm font-medium">Concluído</span>
                  <span class="rounded bg-green-500/10 px-2 py-0.5 text-xs text-green-600 dark:text-green-400">4</span>
                </div>
                <div class="space-y-2">
                  <div class="rounded-md border border-border bg-card p-3 opacity-60 shadow-sm">
                    <p class="text-sm font-medium line-through">Definir escopo</p>
                    <p class="mt-1 text-xs text-muted-foreground">Planning</p>
                  </div>
                  <div class="rounded-md border border-border bg-card p-3 opacity-60 shadow-sm">
                    <p class="text-sm font-medium line-through">Criar repositório</p>
                    <p class="mt-1 text-xs text-muted-foreground">Dev</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Decorative glow -->
          <div class="absolute -inset-x-20 -bottom-20 -z-10 h-40 bg-gradient-to-t from-primary/5 to-transparent blur-2xl" />
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="border-t border-border bg-muted/30 py-20 lg:py-32">
      <div class="container mx-auto px-4">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="mb-4 text-3xl font-bold tracking-tight sm:text-4xl">
            Tudo que você precisa para
            <span class="text-primary">acelerar</span>
            seus projetos
          </h2>
          <p class="text-lg text-muted-foreground">
            Ferramentas poderosas e intuitivas para você e sua equipe entregarem mais, em menos tempo.
          </p>
        </div>

        <div class="mx-auto mt-16 grid max-w-5xl gap-8 sm:grid-cols-2">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="group rounded-xl border border-border bg-card p-6 transition-all hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5"
          >
            <div class="mb-4 flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
              <component :is="feature.icon" class="size-6" />
            </div>
            <h3 class="mb-2 text-xl font-semibold">{{ feature.title }}</h3>
            <p class="text-muted-foreground">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-32">
      <div class="container mx-auto px-4">
        <div class="mx-auto max-w-3xl rounded-2xl bg-gradient-to-br from-primary to-cyan-600 p-8 text-center lg:p-16">
          <h2 class="mb-4 text-3xl font-bold text-white sm:text-4xl">
            Pronto para acelerar seus projetos?
          </h2>
          <p class="mb-8 text-lg text-white/80">
            Comece gratuitamente e veja a diferença que o Sprinter pode fazer.
          </p>
          <Button size="lg" variant="secondary" as-child class="gap-2">
            <RouterLink to="/register">
              Criar conta grátis
              <ArrowRight class="size-4" />
            </RouterLink>
          </Button>
        </div>
      </div>
    </section>
  </DefaultLayout>
</template>
