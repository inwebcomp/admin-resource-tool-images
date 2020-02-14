<template>
    <component :is="zoom ? 'a' : 'div'" :href="zoom" target="_blank" class="gallery__element gallery__image">
        <slot></slot>

        <div class="gallery__element__overlay gallery__element__overlay--transparent">
            <div class="gallery__element__remove" @click.stop.prevent="$emit('remove')">
                <i class="far fa-trash-alt"></i>
            </div>
            <i v-if="zoom" class="fal fa-search-plus gallery__element__overlay__zoom"></i>
        </div>

        <div class="gallery__element__main" :class="{'gallery__element__main--active': main}" :title="__('Главное изображение')" @click.prevent="! main && $emit('setMain')">
            <i class="fa-star" :class="main ? 'fas' : 'far'"></i>
        </div>
    </component>
</template>

<script>
    export default {
        name: 'ImageElement',

        props: {
            main: {},
            zoom: {},
        },
    }
</script>
