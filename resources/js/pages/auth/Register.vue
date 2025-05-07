<script setup>
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

function submit() {
  router.post(route('register'), form.value)
}
</script>

<template>
  <AuthCardLayout title="Criar conta" description="Preencha os dados para se registar.">
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Nome</label>
        <input v-model="form.name" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
        <input v-model="form.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Senha</label>
        <input v-model="form.password" type="password" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Confirmar Senha</label>
        <input v-model="form.password_confirmation" type="password" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-2 rounded-lg font-semibold transition">Registar</button>
      <div class="flex flex-col items-center gap-2 mt-4">
        <Link :href="route('login')" class="text-sm text-primary hover:underline">Já tem conta? Entrar</Link>
      </div>
    </form>
  </AuthCardLayout>
</template> 