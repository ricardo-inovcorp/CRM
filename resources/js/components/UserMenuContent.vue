<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings, Moon, Sun, Monitor } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="route('profile.edit')" prefetch as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    
    <!-- Seleção de tema -->
    <DropdownMenuGroup>
        <DropdownMenuLabel class="px-2 pt-0 pb-1 text-xs font-normal">Tema</DropdownMenuLabel>
        <DropdownMenuItem @click="updateAppearance('light')" :class="appearance === 'light' ? 'bg-accent' : ''">
            <Sun class="mr-2 h-4 w-4" />
            Claro
            <span v-if="appearance === 'light'" class="ml-auto text-xs">✓</span>
        </DropdownMenuItem>
        <DropdownMenuItem @click="updateAppearance('dark')" :class="appearance === 'dark' ? 'bg-accent' : ''">
            <Moon class="mr-2 h-4 w-4" />
            Escuro
            <span v-if="appearance === 'dark'" class="ml-auto text-xs">✓</span>
        </DropdownMenuItem>
        <DropdownMenuItem @click="updateAppearance('system')" :class="appearance === 'system' ? 'bg-accent' : ''">
            <Monitor class="mr-2 h-4 w-4" />
            Sistema
            <span v-if="appearance === 'system'" class="ml-auto text-xs">✓</span>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    
    <DropdownMenuItem :as-child="true">
        <Link class="block w-full" method="post" :href="route('logout')" @click="handleLogout" as="button">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
