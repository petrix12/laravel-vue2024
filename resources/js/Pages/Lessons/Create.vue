<script>
export default {
    name: 'LessonsCreate'
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import LessonForm from '@/Components/Lessons/Form.vue'

defineProps({
    categories: {
        type: Object,
        required: true
    },
    levels: {
        type: Object,
        required: true
    }
})

const form = useForm({
    name: ''
})
</script>

<template>
    <AppLayout title="Create lesson">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create lesson
            </h2>
        </template>
        <div class="py-12 ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <LessonForm 
                        :form="form" 
                        :categories="categories"
                        :levels="levels"
                        @submit="form.post(route('lessons.store'))"
                    />
                </div>
            </div>
        </div>        
    </AppLayout>
</template>