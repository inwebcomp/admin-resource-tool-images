<template>
    <div>
        <div v-if="label" class="form__group__label mt-0">{{ label }}</div>

        <load-area @load="$emit('input', $event)" :maxSize="maxSize" :accept="accept" @clear="$emit('clear')" :clear="clear">
            <slot :image="value"></slot>
        </load-area>
    </div>
</template>

<script>
    import LoadArea from "./LoadArea"

    export default {
        name: 'ImageUploadField',

        components: {
            LoadArea
        },

        props: {
            maxSize: {},
            accept: {},
            label: {},
            value: {},
            clear: {},
        },
    }
</script>
