<script setup lang="ts">
import { router, useForm, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Tasks',
        href: '/tasks',
    },
];

const props = defineProps<{
    tasks: any[];
    filters: any;
}>();

const filterStatus = ref(props.filters?.status || 'all');
const filterPriority = ref(props.filters?.priority || '');
const filterDate = ref(props.filters?.due_date || '');

watch([filterStatus, filterPriority, filterDate], () => {
    router.get(
        '/tasks',
        {
            status: filterStatus.value,
            priority: filterPriority.value,
            due_date: filterDate.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

const showFormModal = ref(false);
const showDetailsModal = ref(false);
const isEditing = ref(false);
const selectedTask = ref<any>(null);

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    priority: 'medium',
    status: 'pending',
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    form.due_date = `${yyyy}-${mm}-${dd}`;

    showFormModal.value = true;
};

const openEditModal = (task: any) => {
    isEditing.value = true;
    selectedTask.value = task;
    form.title = task.title;
    form.description = task.description;
    form.due_date = task.due_date ? task.due_date.split('T')[0] : '';
    form.priority = task.priority;
    form.status = task.status;
    showFormModal.value = true;
};

const openDetailsModal = (task: any) => {
    selectedTask.value = task;
    showDetailsModal.value = true;
};

const saveTask = () => {
    if (isEditing.value) {
        form.put(`/tasks/${selectedTask.value.id}`, {
            onSuccess: () => (showFormModal.value = false),
        });
    } else {
        form.post('/tasks', {
            onSuccess: () => (showFormModal.value = false),
        });
    }
};

const toggleStatus = (task: any) => {
    router.put(
        `/tasks/${task.id}`,
        {
            ...task,
            status: task.status === 'completed' ? 'pending' : 'completed',
        },
        { preserveScroll: true },
    );
};

const deleteTask = (id: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(`/tasks/${id}`, { preserveScroll: true });
    }
};

const priorityColors: Record<string, string> = {
    high: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50',
    medium: 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800/50',
    low: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50',
};

const priorityLabels: Record<string, string> = {
    high: 'High',
    medium: 'Medium',
    low: 'Low',
};
</script>

<template>
    <Head title="My Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8"
        >
            <div
                class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-[#EDEDEC]"
                    >
                        Task Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-[#A1A09A]">
                        Organize your daily activities efficiently.
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center rounded-lg bg-black px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200"
                >
                    <svg
                        class="mr-2 h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        ></path>
                    </svg>
                    New Task
                </button>
            </div>

            <div
                class="flex flex-col gap-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:flex-row dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <div class="flex-1">
                    <label
                        class="mb-1 block text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-[#A1A09A]"
                        >Status</label
                    >
                    <select
                        v-model="filterStatus"
                        class="py-2 px-2 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                    >
                        <option value="all">All Tasks</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label
                        class="mb-1 block text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-[#A1A09A]"
                        >Priority</label
                    >
                    <select
                        v-model="filterPriority"
                        class="py-2 px-2 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                    >
                        <option value="">Any Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label
                        class="mb-1 block text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-[#A1A09A]"
                        >Due Date</label
                    >
                    <input
                        type="date"
                        v-model="filterDate"
                        class="py-2 px-2 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                    />
                </div>
            </div>

            <div
                v-if="tasks.length > 0"
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="task in tasks"
                    :key="task.id"
                    class="group flex flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition-all duration-200 hover:shadow-md dark:border-[#3E3E3A] dark:bg-[#161615]"
                    :class="{
                        'bg-gray-50 opacity-60 dark:bg-[#0a0a0a]':
                            task.status === 'completed',
                    }"
                >
                    <div
                        class="flex-1 cursor-pointer p-5"
                        @click="openDetailsModal(task)"
                    >
                        <div class="mb-3 flex items-start justify-between">
                            <button
                                @click.stop="toggleStatus(task)"
                                class="mt-1 shrink-0 text-gray-400 transition-colors hover:text-green-600 dark:text-gray-500 dark:hover:text-green-400"
                            >
                                <svg
                                    v-if="task.status === 'completed'"
                                    class="h-6 w-6 text-green-500 dark:text-green-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <svg
                                    v-else
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </button>

                            <div class="flex gap-2 text-xs font-medium">
                                <span
                                    :class="[
                                        'rounded-full border px-2.5 py-1',
                                        priorityColors[task.priority],
                                    ]"
                                >
                                    {{ priorityLabels[task.priority] }}
                                </span>
                            </div>
                        </div>

                        <h3
                            :class="[
                                'mb-2 line-clamp-2 text-lg leading-tight font-semibold text-gray-900 dark:text-[#EDEDEC]',
                                {
                                    'text-gray-400 line-through dark:text-gray-600':
                                        task.status === 'completed',
                                },
                            ]"
                        >
                            {{ task.title }}
                        </h3>

                        <p
                            v-if="task.due_date"
                            class="mt-auto flex items-center pt-4 text-sm text-gray-500 dark:text-[#A1A09A]"
                        >
                            <svg
                                class="mr-1.5 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                ></path>
                            </svg>
                            {{
                                new Date(task.due_date).toLocaleDateString(
                                    'en-US',
                                )
                            }}
                        </p>
                    </div>

                    <div
                        class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-5 py-3 opacity-0 transition-opacity group-hover:opacity-100 dark:border-[#3E3E3A] dark:bg-[#0a0a0a]"
                    >
                        <button
                            @click.stop="openEditModal(task)"
                            class="text-sm font-medium text-gray-600 transition-colors hover:text-black dark:text-gray-400 dark:hover:text-white"
                        >
                            Edit
                        </button>
                        <button
                            @click.stop="deleteTask(task.id)"
                            class="text-sm font-medium text-red-600 transition-colors hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-gray-200 bg-white py-20 text-center dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <svg
                    class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    ></path>
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-[#EDEDEC]"
                >
                    No tasks found
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-[#A1A09A]">
                    Create a new task or adjust the filters above.
                </p>
            </div>
        </div>

        <div
            v-if="showFormModal"
            class="relative z-50"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-900/75 transition-opacity dark:bg-black/80"
                @click="showFormModal = false"
            ></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        class="relative transform overflow-hidden rounded-2xl border bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:border-[#3E3E3A] dark:bg-[#161615]"
                    >
                        <form @submit.prevent="saveTask">
                            <div class="px-6 pt-6 pb-4 sm:p-8">
                                <h3
                                    class="mb-6 text-xl leading-6 font-bold text-gray-900 dark:text-[#EDEDEC]"
                                    id="modal-title"
                                >
                                    {{
                                        isEditing
                                            ? 'Edit Task'
                                            : 'Create New Task'
                                    }}
                                </h3>

                                <div class="space-y-5">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-[#A1A09A]"
                                            >Title *</label
                                        >
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            required
                                            class="px-2 py-2 mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black sm:text-sm dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                                        />
                                        <span
                                            v-if="form.errors.title"
                                            class="mt-1 text-xs text-red-500 dark:text-red-400"
                                            >{{ form.errors.title }}</span
                                        >
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-[#A1A09A]"
                                            >Description (Optional)</label
                                        >
                                        <textarea
                                            v-model="form.description"
                                            rows="3"
                                            class="px-2 py-2 mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black sm:text-sm dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-[#A1A09A]"
                                                >Due Date</label
                                            >
                                            <input
                                                v-model="form.due_date"
                                                type="date"
                                                class="px-2 py-2 mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black sm:text-sm dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-[#A1A09A]"
                                                >Priority</label
                                            >
                                            <select
                                                v-model="form.priority"
                                                class="px-2 py-2 mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm transition-colors focus:border-black focus:ring-black sm:text-sm dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] dark:focus:border-white dark:focus:ring-white"
                                            >
                                                <option value="low">Low</option>
                                                <option value="medium">
                                                    Medium
                                                </option>
                                                <option value="high">
                                                    High
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="rounded-b-2xl border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse dark:border-[#3E3E3A] dark:bg-[#0a0a0a]"
                            >
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex w-full justify-center rounded-lg border border-transparent bg-black px-5 py-2.5 text-base font-medium text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm dark:bg-white dark:text-black dark:hover:bg-gray-200"
                                >
                                    Save Task
                                </button>
                                <button
                                    type="button"
                                    @click="showFormModal = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-base font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:hover:bg-[#222]"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showDetailsModal && selectedTask"
            class="relative z-50"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-900/75 transition-opacity dark:bg-black/80"
                @click="showDetailsModal = false"
            ></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        class="relative transform overflow-hidden rounded-2xl border bg-white p-8 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:border-[#3E3E3A] dark:bg-[#161615]"
                    >
                        <div class="mb-6 flex items-start justify-between">
                            <div class="flex gap-2">
                                <span
                                    :class="[
                                        'rounded-full border px-3 py-1 text-xs font-semibold',
                                        priorityColors[selectedTask.priority],
                                    ]"
                                >
                                    {{
                                        priorityLabels[selectedTask.priority]
                                    }}
                                    Priority
                                </span>
                                <span
                                    v-if="selectedTask.status === 'completed'"
                                    class="rounded-full border-green-200 bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:border-green-800/50 dark:bg-green-900/30 dark:text-green-400"
                                >
                                    Completed
                                </span>
                            </div>
                            <button
                                @click="showDetailsModal = false"
                                class="text-gray-400 transition-colors hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-300"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <h2
                            class="mb-4 text-2xl font-bold text-gray-900 dark:text-[#EDEDEC]"
                        >
                            {{ selectedTask.title }}
                        </h2>

                        <div
                            class="prose prose-sm mb-8 whitespace-pre-wrap text-gray-600 dark:text-[#A1A09A]"
                        >
                            {{
                                selectedTask.description ||
                                'No description provided for this task.'
                            }}
                        </div>

                        <div
                            v-if="selectedTask.due_date"
                            class="flex items-center rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
                        >
                            <svg
                                class="mr-2 h-5 w-5 text-gray-400 dark:text-gray-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                ></path>
                            </svg>
                            <span class="mr-1 font-medium">Due:</span>
                            {{
                                new Date(
                                    selectedTask.due_date,
                                ).toLocaleDateString('en-US')
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
