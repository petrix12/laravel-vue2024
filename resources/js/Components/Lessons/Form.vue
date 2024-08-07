<script>
export default {
    name: 'LessonsForm'
}
</script>

<script setup>
import FormSection from '@/Components/FormSection.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import CollectionSelector from '@/Components/Common/CollectionSelector.vue'
import { ref } from 'vue'

defineProps({
    form: {
        type: Object,
        required: true
    },
    updating: {
        type: Boolean,
        required: false,
        default: false
    },
    categories: {
        type: Object,
        required: true
    },
    levels: {
        type: Object,
        required: true
    }
})

const categoriesSelected = ref([])

const onCategories = (_categories) => {
    categoriesSelected.value = _categories
}

defineEmits(['submit'])
</script>

<template>
    <FormSection @submitted="$emit('submit')">
        <template #title>
            {{ updating ? 'Update Lesson' : 'Create Lesson' }}
        </template>
        <template #description>
            {{ updating ? 'Update your lesson' : 'Create a new lesson' }}
        </template>
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                    required
                />
                <InputError :message="$page.props.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="description" value="Description" />
                <TextInput
                    id="description"
                    v-model="form.description"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="description"
                    required
                />
                <InputError :message="$page.props.errors.description" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="content_uri" value="Content uri" />
                <TextInput
                    id="content_uri"
                    v-model="form.content_uri"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="content_uri"
                    required
                />
                <InputError :message="$page.props.errors.content_uri" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <SecondaryButton
                    type="button"
                    class="mt-1 block w-full"
                >
                    Upload PDF
                </SecondaryButton>
                <InputError :message="$page.props.errors.pdf_uri" class="mt-2" />
            </div>
            <div class="flex col-span-6 sm:col-span-4 w-full">
                <div class="w-1/2 mr-1">
                    <InputLabel for="level_id" value="Nivel" />
                    <select class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option 
                            v-for="level in levels" 
                            :key="level.id" 
                            :value="level.id" 
                            id="level_id"
                        >
                            {{ level.name }}
                        </option>
                    </select>
                    <InputError :message="$page.props.errors.level_id" class="mt-2" />
                </div>
                <div class="w-1/2 ml-1">
                    <InputLabel for="categories" value="Categories" />
                    <CollectionSelector 
                        name="categories" 
                        id="categories" 
                        :collection="categories" 
                        @onCategories="onCategories"
                    />
                </div>
            </div>
        </template>
        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Update' : 'Create' }}                
            </PrimaryButton>
        </template>
    </FormSection>
</template>