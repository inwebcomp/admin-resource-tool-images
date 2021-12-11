<template>
    <div class="gallery__element gallery__image">
        <slot></slot>

        <component :is="zoom ? 'a' : 'div'" target="_blank" :href="zoom" class="gallery__element__overlay gallery__element__overlay--transparent">
            <div class="gallery__element__remove" @click.stop.prevent="$emit('remove')">
                <i class="far fa-trash-alt"></i>
            </div>

            <i v-if="zoom" class="fal fa-search-plus gallery__element__overlay__zoom"></i>

            <div class="gallery__element__overlay__size" v-if="sizeInfo && sizeInfo.width && sizeInfo.height">
                {{ sizeInfo.width }} x {{ sizeInfo.height }}
            </div>
        </component>

        <div class="gallery__element__main" :class="{'gallery__element__main--active': main}"
             :title="main ? __('Главное изображение') : __('Сделать главным')" @click.prevent="! main && $emit('setMain')">
            <i class="fa-star" :class="main ? 'fas' : 'far'"></i>
        </div>

        <app-select v-if="languages" small
                    class="gallery__element__language"
                    :emptyTitle="'--'"
                    @select="$emit('setLanguage', $event)"
                    :options="languageOptions"
                    :value="language"
                    withEmpty
                    :search="false"/>
    </div>
</template>

<script>
    export default {
        name: 'ImageElement',

        props: {
            main: {},
            language: {},
            zoom: {},
            languages: {},
            sizeInfo: {},
        },

        data: () => ({
            languageOptions: [],
        }),

        created() {
            if (this.languages) {
                this.languageOptions = this.languages.map(item => ({
                    value: item,
                    title: item.toUpperCase(),
                }))
            }
        },
    }
</script>
