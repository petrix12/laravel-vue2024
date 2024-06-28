<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />   
            <h1>Soluciones++</h1>
            <p>{{ $page.props }}</p>
        </div>
    </div>
</template>
