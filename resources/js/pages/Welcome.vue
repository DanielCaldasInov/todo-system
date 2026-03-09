<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Sun, Moon } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const isDark = ref(false);

onMounted(() => {
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};
</script>

<template>
    <Head title="Welcome" />

    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] transition-colors duration-300 lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-83.75 text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <button
                    @click="toggleTheme"
                    class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#A1A09A] dark:hover:bg-[#222] dark:hover:text-white"
                    aria-label="Toggle Dark Mode"
                >
                    <Sun v-if="isDark" class="h-4 w-4" />
                    <Moon v-else class="h-4 w-4" />
                </button>

                <div class="h-4 w-px bg-gray-300 dark:bg-[#3E3E3A]"></div>

                <Link
                    v-if="$page.props.auth.user"
                    href="/tasks"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    My Tasks
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] transition-colors hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow"
        >
            <main
                class="flex w-full max-w-83.75 flex-col-reverse overflow-hidden rounded-lg border border-gray-100 shadow-[0px_4px_24px_rgba(0,0,0,0.04)] lg:max-w-4xl lg:flex-row dark:border-[#222]"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-8 pb-12 text-[13px] leading-5 transition-colors duration-300 lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC]"
                >
                    <h1
                        class="mb-3 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl dark:text-white"
                    >
                        Stay Organized, Stay Ahead.
                    </h1>
                    <p
                        class="mb-6 text-base text-[#706f6c] dark:text-[#A1A09A]"
                    >
                        A minimal and efficient task management system designed
                        to help you focus on what truly matters.
                    </p>

                    <ul
                        class="mb-8 flex flex-col gap-4 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <li class="flex items-center gap-3">
                            <svg
                                class="h-5 w-5 text-green-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                ></path>
                            </svg>
                            Track your daily activities with ease.
                        </li>
                        <li class="flex items-center gap-3">
                            <svg
                                class="h-5 w-5 text-green-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                ></path>
                            </svg>
                            Set priorities and never miss a deadline.
                        </li>
                        <li class="flex items-center gap-3">
                            <svg
                                class="h-5 w-5 text-green-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                ></path>
                            </svg>
                            Clean, distraction-free interface.
                        </li>
                    </ul>

                    <div class="flex gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            href="/tasks"
                            class="inline-block rounded-md bg-black px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200"
                        >
                            Go to My Tasks
                        </Link>
                        <Link
                            v-else
                            :href="register()"
                            class="inline-block rounded-md bg-black px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200"
                        >
                            Start for free
                        </Link>
                    </div>
                </div>

                <div
                    class="relative flex w-full shrink-0 items-center justify-center overflow-hidden rounded-t-lg border-l border-gray-100 bg-gray-50 p-12 transition-colors duration-300 lg:w-109.5 lg:rounded-t-none lg:rounded-r-lg dark:border-[#222] dark:bg-[#1a1a19]"
                >
                    <div
                        class="w-full max-w-60 rotate-2 transform rounded-xl border border-gray-100 bg-white p-6 shadow-lg transition-transform duration-300 hover:rotate-0 dark:border-[#333] dark:bg-[#222]"
                    >
                        <div
                            class="mb-6 h-1.5 w-12 rounded-full bg-gray-200 dark:bg-gray-600"
                        ></div>

                        <div class="mb-5 flex items-center gap-4">
                            <div
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
                            >
                                <svg
                                    class="h-3 w-3 text-green-600 dark:text-green-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <div
                                class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-700"
                            ></div>
                        </div>

                        <div class="mb-5 flex items-center gap-4">
                            <div
                                class="h-5 w-5 shrink-0 rounded-full border-2 border-gray-200 dark:border-gray-600"
                            ></div>
                            <div
                                class="h-2 w-3/4 rounded-full bg-gray-200 dark:bg-gray-600"
                            ></div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div
                                class="h-5 w-5 shrink-0 rounded-full border-2 border-gray-200 dark:border-gray-600"
                            ></div>
                            <div
                                class="h-2 w-5/6 rounded-full bg-gray-200 dark:bg-gray-600"
                            ></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
