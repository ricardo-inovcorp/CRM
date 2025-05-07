<script setup lang="ts">
import Layout from '@/layouts/settings/Layout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Sun, Moon, Monitor } from 'lucide-vue-next';

const theme = ref(localStorage.getItem('theme') || 'system');
function setTheme(value: string) {
  theme.value = value;
  localStorage.setItem('theme', value);
  document.documentElement.setAttribute('data-theme', value);
  document.documentElement.classList.toggle('dark', value === 'dark' || (value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches));
}
</script>
<template>
  <Head title="Aparência" />
  <Layout>
    <div class="space-y-6 max-w-lg">
      <div>
        <h2 class="text-xl font-semibold mb-4">Configurações de aparência</h2>
        <p class="text-muted-foreground mb-6">Personalize a aparência visual do sistema com as opções de tema.</p>
      </div>
      
      <div class="space-y-4">
        <div>
          <h3 class="text-lg font-medium mb-3">Tema</h3>
          <div class="grid grid-cols-3 gap-4">
            <div 
              class="flex flex-col items-center gap-2 p-4 border rounded-lg cursor-pointer transition-colors"
              :class="{'bg-muted border-primary': theme === 'light', 'border-border': theme !== 'light'}"
              @click="setTheme('light')"
            >
              <div class="h-10 w-10 rounded-full bg-background flex items-center justify-center">
                <Sun class="h-5 w-5" />
              </div>
              <span class="text-sm font-medium">Claro</span>
            </div>
            
            <div 
              class="flex flex-col items-center gap-2 p-4 border rounded-lg cursor-pointer transition-colors"
              :class="{'bg-muted border-primary': theme === 'dark', 'border-border': theme !== 'dark'}"
              @click="setTheme('dark')"
            >
              <div class="h-10 w-10 rounded-full bg-background flex items-center justify-center">
                <Moon class="h-5 w-5" />
              </div>
              <span class="text-sm font-medium">Escuro</span>
            </div>
            
            <div 
              class="flex flex-col items-center gap-2 p-4 border rounded-lg cursor-pointer transition-colors"
              :class="{'bg-muted border-primary': theme === 'system', 'border-border': theme !== 'system'}"
              @click="setTheme('system')"
            >
              <div class="h-10 w-10 rounded-full bg-background flex items-center justify-center">
                <Monitor class="h-5 w-5" />
              </div>
              <span class="text-sm font-medium">Sistema</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="text-sm text-muted-foreground mt-6">
        A alteração do tema é aplicada imediatamente e salva no navegador.
      </div>
    </div>
  </Layout>
</template> 