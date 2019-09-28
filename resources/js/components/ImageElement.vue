<template>
    <div class="gallery__element gallery__image">
        <slot></slot>

        <div class="gallery__element__overlay gallery__element__overlay--transparent" @click="$emit('upload')">
            <div class="gallery__element__remove" @click.stop="$emit('remove')">
                <i class="far fa-trash-alt"></i>
            </div>
        </div>

        <div class="gallery__element__main" :class="{'gallery__element__main--active': main}" :title="__('Главное изображение')" @click="$emit('setMain')">
            <i class="fa-star" :class="main ? 'fas' : 'far'"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ImageElement',

        props: {
            main: {},
        },
    }
</script>
