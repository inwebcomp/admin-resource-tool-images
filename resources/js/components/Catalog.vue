<template>
    <div class="gallery__catalog flex flex-wrap -mx-2">
        <image-element class="mx-2 mb-4" v-for="(image, $i) in images" :key="$i" @remove="$emit('remove', $i)" @setMain="$emit('setMain', $i)" :main="image.main">
            <img :src="image.url" alt="">
        </image-element>
    </div>
</template>

<script>
    import ImageElement from "./ImageElement"

    export default {
        name: "Catalog",
        components: {ImageElement},
        props: {
            images: {}
        }
    }
</script>