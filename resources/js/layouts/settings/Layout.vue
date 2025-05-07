<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { UserRound, KeyRound, Palette, ArrowLeft } from 'lucide-vue-next';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Perfil',
        href: '/settings/profile',
        icon: UserRound,
    },
    {
        title: 'Senha',
        href: '/settings/password',
        icon: KeyRound, 
    },
    {
        title: 'Aparência',
        href: '/settings/appearance',
        icon: Palette,
    },
];

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <div class="px-4 py-6">
                <div class="flex items-center justify-between mb-6">
                    <Heading title="Configurações" description="Gerencie seu perfil e configurações da conta" />
                    <Link href="/dashboard" class="flex items-center text-sm font-medium text-muted-foreground hover:text-foreground">
                        <ArrowLeft class="h-4 w-4 mr-1" />
                        Voltar ao CRM
                    </Link>
                </div>

                <div class="flex flex-col space-y-8 mt-6 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
                    <aside class="w-full lg:w-64">
                        <nav class="flex flex-col space-y-2">
                            <Button
                                v-for="item in sidebarNavItems"
                                :key="item.href"
                                variant="ghost"
                                :class="['w-full justify-start rounded-lg', { 'bg-muted': currentPath === item.href }]"
                                as-child
                            >
                                <Link :href="item.href" class="flex items-center px-3 py-2 w-full">
                                    <component :is="item.icon" class="h-5 w-5 mr-3" />
                                    {{ item.title }}
                                </Link>
                            </Button>
                        </nav>
                    </aside>

                    <Separator class="my-6 md:hidden" orientation="horizontal" />

                    <div class="flex-1 md:max-w-3xl">
                        <section class="max-w-3xl">
                            <slot />
                        </section>
                    </div>
                </div>
            </div>
        </AppContent>
    </AppShell>
</template>
