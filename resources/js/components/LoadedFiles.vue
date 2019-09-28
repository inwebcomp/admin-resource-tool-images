<template>
    <div class="gallery__loaded-files flex flex-wrap -mx-2">
        <element-for-load class="mx-2 mb-4" v-for="(image, $i) in images" :key="$i" :loading="image.loading" :errors="image.errors" @remove="$emit('remove', $i)" @upload="$emit('upload', $i)">
            <img :src="image.full_urls.default" alt="">
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
        }
    }
</script>