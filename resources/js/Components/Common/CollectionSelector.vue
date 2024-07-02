<script setup>
import { ref } from 'vue'

defineProps({
    collection: {
        type: Array,
        required: true
    }
})

const currentSelection = ref(1)
const selection = ref([])
const emit = defineEmits(['onCategories'])

const handleAddToSelection = () => {
    let alreadyExists = false
    selection.value.forEach((item) => {
        if (item.id === currentSelection.value.id) {
            alreadyExists = true
            return
        }
    })
    if (alreadyExists) {
        return
    }
    selection.value.push(currentSelection.value)
    emit('onCategories', selection.value)
}

const handleRemoveSelection = (index) => {
    selection.value = selection.value.filter((item) => item.id !== index)
    emit('onCategories', selection.value)
}
</script>

<template>
    <div class="w-full">
        <div class="flex">
            <select v-model="currentSelection" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option v-for="(item, index) in collection" :key="index" :value="item">{{ item?.name }}</option>
            </select>
            <button 
                @click="handleAddToSelection" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-1"
            >
                Add
            </button>
        </div>
        <div>
            <ul>
                <li 
                    v-for="(item) in selection" 
                    :key="item.id"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-1 mt-1 mb-1"
                >
                    {{ item.name }} 
                    <span class="float-right cursor-pointer" @click="handleRemoveSelection(item.id)">X</span>
                </li>
            </ul>
        </div>
    </div>
</template>