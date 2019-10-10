<template>
    <div class="gallery__loaded-files flex flex-wrap -mx-2">
        <element-for-load class="mx-2 mb-4" v-for="(image, $i) in images" :key="$i" :loading="image.loading" :errors="image.errors" @remove="$emit('remove', $i)" @upload="$emit('upload', $i)">
            <img v-if="isImage(image.type)" :src="image.full_urls.default" alt="">
            <div v-if="! isImage(image.type)" class="p-4">{{ image.name }}</div>
        </element-for-load>

        <hr class="gallery__hr mt-2 mb-6">
    </div>
</template>

<script>
    import ElementForLoad from "./ElementForLoad"

    export default {
        name: "LoadedFiles",
        components: {ElementForLoad},

        props: {
            images: {},
        },

        methods: {
            isImage(type) {
                return ['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'].includes(type)
            }
        },
    }
</script>