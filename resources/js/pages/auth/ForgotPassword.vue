<script setup>
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const form = ref({
  email: ''
})
const status = ref('')
const errors = ref({})

function submit() {
  router.post(route('password.email'), form.value, {
    onSuccess: (page) => {
      status.value = page.props.status || 'Se o email estiver registado, um link de recuperação foi enviado.'
    },
    onError: (err) => {
      errors.value = err
    }
  })
}
</script>

<template>
  <AuthCardLayout title="Recuperar senha" description="Informe seu email para receber o link de recuperação.">
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
        <input v-model="form.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
        <div v-if="errors.email" class="text-sm text-red-500 mt-1">{{ errors.email }}</div>
      </div>
      <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-gray-900 py-2 rounded-lg font-semibold transition">Enviar link de recuperação</button>
      <div v-if="status" class="text-green-600 text-center mt-2">{{ status }}</div>
      <div class="flex flex-col items-center gap-2 mt-4">
        <Link :href="route('login')" class="text-sm text-primary hover:underline">Voltar ao login</Link>
      </div>
    </form>
  </AuthCardLayout>
</template> 