<template>
    <draggable v-bind="dragOptions" :value="images" @input="$emit('changePositions', $event)" class="gallery__catalog flex flex-wrap -mx-2">
        <image-element class="mx-2 mb-4" v-for="(image, $i) in images" :key="$i" @remove="$emit('remove', $i)" @setMain="$emit('setMain', $i)" :main="image.main" :zoom="image.url">
            <img :src="image.url" alt="">
        </image-element>
    </draggable>
</template>

<script>
    import ImageElement from "./ImageElement"
    import Draggable from 'vuedraggable'

    export default {
        name: "Catalog",
        components: {ImageElement,Draggable},

        props: {
            images: {}
        },

        computed: {
            dragOptions() {
                return {
                    delay: 0,
                    touchStartThreshold: 0,
                    forceFallback: true,
                    animation: 150,
                    ghostClass: "ghost",
                    dragClass: "sortable-drag",
                }
            }
        }
    }
</script>

<style>
    .gallery__catalog .sortable-drag {
        opacity: 1 !important;
        visibility: visible;
    }

    .gallery__catalog .ghost {
        opacity: 0 !important;
        visibility: hidden;
    }
</style>