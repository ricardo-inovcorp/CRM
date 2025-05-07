<template>
  <AuthCardLayout title="Entrar no sistema" description="Acesse com seu email e senha.">
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
        <input v-model="form.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Senha</label>
        <input v-model="form.password" type="password" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
      </div>
      <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-gray-900 py-2 rounded-lg font-semibold transition">Entrar</button>
      <div class="flex flex-col items-center gap-2 mt-4">
        <Link :href="route('register')" class="text-sm text-primary font-semibold hover:underline">Ainda não tem conta? Registar.</Link>
        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-primary font-semibold hover:underline">Esqueceu a senha?</Link>
      </div>
    </form>
  </AuthCardLayout>
</template>

<script setup>
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue'
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

const form = ref({
  email: '',
  password: ''
})

const page = usePage();
const canResetPassword = computed(() => page.props.canResetPassword ?? false)

function submit() {
  router.post(route('login'), form.value)
}
</script> 