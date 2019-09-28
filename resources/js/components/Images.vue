<template>
    <div class="gallery">
        <load-area @load="load" class="mb-4"/>

        <loaded-files v-if="loadedImages.length" :images="loadedImages" @remove="removeLoaded"
                      @upload="upload"></loaded-files>

        <catalog :images="images" @remove="remove" @setMain="setMain"></catalog>
    </div>
</template>

<script>
    import LoadArea from "./LoadArea"
    import LoadedFiles from "./LoadedFiles"
    import Catalog from "./Catalog"

    export default {
        components: {Catalog, LoadedFiles, LoadArea},

        props: {
            resourceName: {},
            resourceId: {},
            multiple: {
                type: Boolean,
                default: true
            },
        },

        data() {
            return {
                loadedImages: [],
                images: [],
                loading: false,
                loadingRemove: false,
                loadingSetMain: false,
            }
        },

        created() {
            this.fetch()
        },

        methods: {
            fetch() {
                App.api.request({
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId,
                }).then(({images}) => {
                    this.images = images
                })
            },

            removeLoaded(index) {
                this.loadedImages = this.loadedImages.filter((value, i) => i !== index)
            },

            load(files) {
                Array.from(files).forEach(file => {
                    let errors = file.errors
                    file = new File([file], file.name, {type: file.type})
                    let reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = () => {
                        const fileData = {
                            file: file,
                            full_urls: {
                                default: reader.result,
                            },
                            name: file.name,
                            file_name: file.name,
                            errors: errors,
                            loading: false
                        }
                        if (this.multiple) {
                            this.loadedImages.push(fileData)
                        } else {
                            this.loadedImages = [fileData]
                        }

                        this.upload()
                    }
                })
            },

            upload(index = 0) {
                if (! this.loadedImages.length) {
                    this.loading = false
                    return
                }

                if (this.loading)
                    return

                this.loading = true

                let file

                while (true) {
                    if (! this.loadedImages[index]) {
                        this.loading = false
                        return;
                    }

                    file = this.loadedImages[index]

                    if (! file.errors.length)
                        break;

                    index++
                }

                file.loading = true

                this.uploadRequest(file, index)
            },

            uploadRequest(file, index) {
                let files = [file]

                files = files.filter(file => ! file.sending)

                if (! files.length)
                    return

                files.forEach(file => file.sending = true)

                App.api.request({
                    method: 'POST',
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId,
                    data: {
                        images: files
                    }
                }).then(({images}) => {
                    App.$emit('imageUploaded', files)
                    App.$emit('indexRefresh')

                    this.loading = false

                    this.loadedImages = this.loadedImages.filter((value, i) => i !== index)

                    this.images.push(...images)

                    this.upload()

                    this.$toasted.show(
                        files.length > 1 ? this.__('The images was uploaded!') : this.__('The image was uploaded!'),
                        {type: 'success'}
                    )
                })
            },

            remove(index) {
                if (this.loadingRemove)
                    return

                if (! confirm(this.__('Are you sure to delete the image?')))
                    return

                this.loadingRemove = true

                let image = this.images.find((value, i) => i === index)
                image.loading = true

                App.api.request({
                    method: 'DELETE',
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId,
                    data: {
                        images: [image.id]
                    }
                }).then(() => {
                    this.loadingRemove = false

                    App.$emit('imageRemoved', image)
                    App.$emit('indexRefresh')

                    this.images = this.images.filter((value, i) => i !== index)

                    if (image.main && this.images.length)
                        this.images[0].main = true

                    this.$toasted.show(
                        this.__('The image was removed!'),
                        {type: 'success'}
                    )
                }).catch(() => {
                    this.loadingRemove = false
                })
            },

            setMain(index) {
                if (this.loadingSetMain)
                    return

                this.loadingSetMain = true

                let image = this.images.find((value, i) => i === index)
                image.loading = true

                App.api.request({
                    method: 'PUT',
                    url: 'resource-tool/images/main/' + image.id,
                }).then(() => {
                    this.loadingSetMain = false

                    App.$emit('imageSetMain', image)
                    App.$emit('indexRefresh')

                    this.images.forEach((value, i) => value.main = i === index)

                    this.$toasted.show(
                        this.__('The image was set as main!'),
                        {type: 'success'}
                    )
                }).catch(() => {
                    this.loadingSetMain = false
                })
            },
        }
    }
</script>
