<script>
export default {
    name: 'LessonsIndex'
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps({
    lessons: {
        type: Object,
        required: true
    }
})

const deleteLesson = (id) => {
    if(confirm('Are you sure?')) {
        Inertia.delete(route('lessons.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Index lesson">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lessons
            </h2>
        </template>
        <div class="py-12 ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div                 
                    v-if="$page.props.user.permissions.includes('create lessons')"
                    class="p-6 bg-white border-b border-gray-200"
                >
                    <div class="flex justify-between">
                        <Link 
                            :href="route('lessons.create')" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Create lesson
                        </Link>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-hidden">
                                    <table class="min-w-full">
                                        <thead class="bg-white border-b">
                                            <tr>
                                            <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Name
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Edit
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Delete
                                            </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-gray-100 border-b" v-for="lesson in lessons.data" :key="lesson.id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ lesson.id }}
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    {{ lesson.name }}
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    <Link 
                                                        v-if="$page.props.user.permissions.includes('update lessons')"
                                                        :href="route('lessons.edit', lesson.id)" 
                                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                    >
                                                        Edit
                                                    </Link>
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    <Link 
                                                        v-if="$page.props.user.permissions.includes('delete lessons')"
                                                        @click="deleteLesson(lesson.id)" 
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                                    >
                                                        Delete
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mt-2">
                        <Link 
                            v-if="lessons.current_page > 1"
                            :href="lessons.prev_page_url" 
                            class="py-2 px-4 rounded"
                        >
                            PREV
                        </Link>
                        <div v-else></div>
                        <Link 
                            v-if="lessons.current_page < lessons.last_page"
                            :href="lessons.next_page_url" 
                            class="py-2 px-4 rounded"
                        >
                            NEXT
                        </Link>
                        <div v-else></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>