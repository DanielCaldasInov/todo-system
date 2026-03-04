<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
// Importamos ícones elegantes do Lucide para as tarefas e para o logótipo
import { ListTodo, CheckCircle } from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Tasks',
        href: '/tasks',
        icon: ListTodo,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/tasks">
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-lg bg-black text-white dark:bg-white dark:text-black"
                            >
                                <CheckCircle class="size-5" />
                            </div>
                            <div
                                class="grid flex-1 text-left text-sm leading-tight"
                            >
                                <span
                                    class="truncate font-semibold text-gray-900 dark:text-[#EDEDEC]"
                                    >To-Do App</span
                                >
                                <span
                                    class="truncate text-xs text-gray-500 dark:text-[#A1A09A]"
                                    >Stay organized</span
                                >
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
