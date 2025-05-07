<script setup lang="ts">
import Layout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { AlertCircle, CheckCircle2 } from 'lucide-vue-next';

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const feedback = ref('');
const feedbackType = ref('');

function submit() {
  form.put(route('profile.password.update'), {
    onSuccess: () => { 
      feedback.value = 'Senha alterada com sucesso!'; 
      feedbackType.value = 'success';
      form.reset(); 
    },
    onError: () => { 
      feedback.value = 'Erro ao alterar senha.'; 
      feedbackType.value = 'error';
    }
  });
}
</script>
<template>
  <Head title="Alterar Senha" />
  <Layout>
    <div class="space-y-6">
      <div>
        <h2 class="text-xl font-semibold mb-4">Atualizar senha</h2>
        <p class="text-muted-foreground mb-6">Garanta que sua conta esteja usando uma senha longa e aleatória para se manter segura.</p>
      </div>
      
      <form @submit.prevent="submit" class="space-y-6 max-w-lg">
        <div v-if="feedback" class="p-4 rounded-lg mb-6" :class="feedbackType === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-950/50 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400'">
          <div class="flex items-center gap-2">
            <CheckCircle2 v-if="feedbackType === 'success'" class="h-5 w-5" />
            <AlertCircle v-else class="h-5 w-5" />
            <span>{{ feedback }}</span>
          </div>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block mb-2 text-sm font-medium">Senha atual</label>
            <input 
              v-model="form.current_password" 
              type="password" 
              class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary" 
              required 
            />
            <div v-if="form.errors.current_password" class="mt-1 text-sm text-red-500">
              {{ form.errors.current_password }}
            </div>
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-medium">Nova senha</label>
            <input 
              v-model="form.password" 
              type="password" 
              class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary" 
              required 
            />
            <div v-if="form.errors.password" class="mt-1 text-sm text-red-500">
              {{ form.errors.password }}
            </div>
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-medium">Confirmar nova senha</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary" 
              required 
            />
          </div>
        </div>
        
        <div class="flex items-center justify-end gap-4 pt-4">
          <button 
            type="submit" 
            class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium"
            :disabled="form.processing"
          >
            Salvar senha
          </button>
        </div>
      </form>
    </div>
  </Layout>
</template> 