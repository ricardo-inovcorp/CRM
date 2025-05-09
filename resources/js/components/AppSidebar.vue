<script setup>
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart2, BookOpen, Briefcase, Calendar, Cog, Folder, LayoutGrid, Users, Building2, UserCog } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
// Check if user is admin using the shared property
const user = page.props.auth.user;
const showAdminMenu = page.props.auth.user && user.isAdmin;

const mainNavItems = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Entidades',
        href: '/entidades',
        icon: Building2,
    },
    {
        title: 'Contactos',
        href: '/contactos',
        icon: Users,
    },
    {
        title: 'Atividades',
        href: route('calendario.index'),
        icon: Calendar,
    },
    {
        title: 'Negócios',
        href: '/negocios',
        icon: Briefcase,
    },
    {
        title: 'Relatórios',
        href: route('relatorios.index'),
        icon: BarChart2,
    },
    // Adicionar link de gestão de usuários apenas para admins
    ...(showAdminMenu ? [
        {
            title: 'Utilizadores',
            href: route('users.index'),
            icon: UserCog,
        }
    ] : []),
    {
        title: 'Configurações',
        href: '/settings/profile',
        icon: Cog,
    },
];

const footerNavItems = [
    {
        title: 'Documentação',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
    {
        title: 'GitHub',
        href: 'https://github.com/laravel/laravel',
        icon: Folder,
    },
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
