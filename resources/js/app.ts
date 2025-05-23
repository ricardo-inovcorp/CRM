import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

console.log('Inicializando aplicação Inertia...');

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        console.log('Tentando resolver componente:', name);
        
        // Import pages from both Pages (capital P) and pages (lowercase p) directories
        const pagesCapital = import.meta.glob('./Pages/**/*.vue', { eager: false });
        const pagesLowercase = import.meta.glob('./pages/**/*.vue', { eager: false });
        
        // Merge both page collections
        const pages = { ...pagesCapital, ...pagesLowercase };
        
        // Log dos componentes disponíveis
        console.log('Componentes disponíveis:', Object.keys(pages));
        
        // Try both capitalized and lowercase paths
        const pathCapital = `./Pages/${name}.vue`;
        const pathLowercase = `./pages/${name}.vue`;
        
        console.log('Procurando componente em:', pathCapital, 'ou', pathLowercase);
        
        try {
            // Try uppercase path first
            if (pages[pathCapital]) {
                return resolvePageComponent(pathCapital, pages);
            }
            // Then try lowercase path
            if (pages[pathLowercase]) {
                return resolvePageComponent(pathLowercase, pages);
            }
            
            // Default to original behavior
            return resolvePageComponent(`./Pages/${name}.vue`, pages);
        } catch (error) {
            console.error('Erro ao resolver componente:', error);
            throw error;
        }
    },
    setup({ el, App, props, plugin }) {
        console.log('Montando app no elemento:', el);
        console.log('Props recebidas:', props);
        
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
        
        console.log('Aplicação montada com sucesso!');
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// Adicionar handler global para erros não capturados
window.addEventListener('error', (event) => {
    console.error('Erro global não capturado:', event.error || event.message);
});

// Adicionar handler para rejeições de promessas não tratadas
window.addEventListener('unhandledrejection', (event) => {
    console.error('Promessa rejeitada não tratada:', event.reason);
});
