<script setup lang="ts">
import Layout from '@/layouts/settings/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { AlertCircle, CheckCircle2 } from 'lucide-vue-next';

const user = usePage().props.auth.user;
const form = useForm({
  name: user.name,
  email: user.email,
});

const feedback = ref('');
const feedbackType = ref('');

function submit() {
  form.patch(route('profile.update'), {
    onSuccess: () => { 
      feedback.value = 'Perfil atualizado com sucesso!'; 
      feedbackType.value = 'success';
    },
    onError: () => { 
      feedback.value = 'Erro ao atualizar perfil.'; 
      feedbackType.value = 'error';
    }
  });
}

// Tema
const theme = ref(localStorage.getItem('theme') || 'system');
function setTheme(value: string) {
  theme.value = value;
  localStorage.setItem('theme', value);
  document.documentElement.setAttribute('data-theme', value);
}

// Email
const emailForm = useForm({
  email: '',
});
const emailFeedback = ref('');
function submitEmail() {
  emailForm.post(route('profile.email.update'), {
    onSuccess: () => { emailFeedback.value = 'Email atualizado com sucesso!'; },
    onError: () => { emailFeedback.value = 'Erro ao atualizar email.'; }
  });
}

// Senha
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});
const passwordFeedback = ref('');
function submitPassword() {
  passwordForm.post(route('profile.password.update'), {
    onSuccess: () => { passwordFeedback.value = 'Senha alterada com sucesso!'; passwordForm.reset(); },
    onError: () => { passwordFeedback.value = 'Erro ao alterar senha.'; }
  });
}
</script>

<template>
  <Head title="Perfil" />
  <Layout>
    <div class="space-y-6">
      <div>
        <h2 class="text-xl font-semibold mb-4">Informações do perfil</h2>
        <p class="text-muted-foreground mb-6">Atualize suas informações de conta e endereço de email.</p>
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
            <label class="block mb-2 text-sm font-medium">Nome</label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary" 
              required 
            />
            <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
              {{ form.errors.name }}
            </div>
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-medium">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary" 
              required 
            />
            <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">
              {{ form.errors.email }}
            </div>
          </div>
        </div>
        
        <div class="flex items-center justify-end gap-4 pt-4">
          <button 
            type="submit" 
            class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium"
            :disabled="form.processing"
          >
            Salvar informações
          </button>
        </div>
      </form>
      
      <div class="border-t border-border mt-10 pt-10">
        <h2 class="text-xl font-semibold mb-4">Excluir conta</h2>
        <p class="text-muted-foreground mb-6">Após excluir sua conta, todos os recursos e dados serão permanentemente excluídos.</p>
        
        <button 
          class="px-4 py-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 font-medium"
          onclick="confirm('Esta ação não pode ser desfeita. Tem certeza que deseja excluir sua conta?') && document.getElementById('delete-account-form').submit()"
        >
          Excluir conta
        </button>
        
        <form id="delete-account-form" method="post" :action="route('profile.destroy')" class="hidden">
          <input type="hidden" name="_method" value="DELETE">
          <input type="hidden" name="_token" :value="$page.props.csrf_token">
        </form>
      </div>
    </div>
  </Layout>
</template> 