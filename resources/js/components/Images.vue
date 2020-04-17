<template>
    <div class="gallery">
        <load-area @load="load" class="mb-4" :max-size="field.maxSize" :accept="field.accept"
                   :multiple="field.multiple"/>
        <div class="text-center my-4">{{ __('или') }}</div>
        <url-area @load="uploadViaUrl" class="mb-4"/>

        <loaded-files v-if="loadedImages.length" :images="loadedImages" @remove="removeLoaded"
                      @upload="upload"></loaded-files>

        <div class="my-4">
            <label class="flex items-center">{{ __('Удаление без подтверждения') }}
                <switch-input class="ml-4" v-model="fastDelete"></switch-input>
            </label>
        </div>

        <catalog :images="images" @remove="remove" @changePositions="changePositions" @setMain="setMain"></catalog>
    </div>
</template>

<script>
    import LoadArea from "./LoadArea"
    import UrlArea from "./UrlArea"
    import LoadedFiles from "./LoadedFiles"
    import Catalog from "./Catalog"

    import axios from 'axios'

    export default {
        components: {
            Catalog,
            LoadedFiles,
            LoadArea,
            UrlArea,
        },

        props: {
            resourceName: {},
            resourceId: {},
            field: {},
        },

        data() {
            return {
                loadedImages: [],
                images: [],
                loading: false,
                loadingRemove: false,
                loadingSetMain: false,
                fastDelete: false,
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
                            progress: 0,
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
                if (!this.loadedImages.length) {
                    this.loading = false
                    return
                }

                if (this.loading)
                    return

                this.loading = true

                let file

                while (true) {
                    if (!this.loadedImages[index]) {
                        this.loading = false
                        return
                    }

                    file = this.loadedImages[index]

                    if (!file.errors.length)
                        break

                    index++
                }

                file.loading = true

                this.uploadRequest(file, index)
            },

            uploadRequest(file, index) {
                let files = [file]

                files = files.filter(file => !file.sending)

                if (!files.length)
                    return

                files.forEach(file => file.sending = true)

                App.api.request({
                    method: 'PUT',
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId,
                    data: {
                        images: files
                    },
                }, {
                    onUploadProgress: (progressEvent) => {
                        files.forEach(file => file.progress = progressEvent.loaded / progressEvent.total)
                    }
                }).then(({images}) => {
                    App.$emit('imageUploaded', files)
                    App.$emit('indexRefresh')

                    this.loading = false

                    this.loadedImages = this.loadedImages.filter((value, i) => i !== index)

                    this.images.push(...images)

                    this.upload()

                    this.$toasted.show(
                        images.length > 1 ? this.__('The images was uploaded!') : this.__('The image was uploaded!'),
                        {type: 'success'}
                    )
                })
            },

            uploadViaUrl(url) {
                if (!url)
                    return

                App.api.request({
                    method: 'PUT',
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId,
                    data: {
                        url,
                    }
                }).then(({images}) => {
                    App.$emit('imageUploaded', url)
                    App.$emit('indexRefresh')

                    this.loading = false

                    this.loadedImages = this.loadedImages.filter((value, i) => i !== index)

                    this.images.push(...images)

                    this.upload()

                    this.$toasted.show(
                        images.length > 1 ? this.__('The images was uploaded!') : this.__('The image was uploaded!'),
                        {type: 'success'}
                    )
                })
            },

            remove(index) {
                if (this.loadingRemove)
                    return

                if (!this.fastDelete) {
                    if (!confirm(this.__('Are you sure to delete the image?')))
                        return
                }

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

            changePositions(images) {
                this.images = images

                if (this.loading)
                    return

                this.loading = true

                App.api.request({
                    method: 'POST',
                    url: 'resource-tool/images/' + this.resourceName + '/' + this.resourceId + '/positions',
                    data: {
                        images: this.images.map(image => image.id)
                    }
                }).then(() => {
                    this.loading = false

                    App.$emit('imageChangePositions')

                    this.$toasted.show(
                        this.__('Images positions were changed'),
                        {type: 'success'}
                    )
                }).catch(() => {
                    this.loading = false
                })
            },
        }
    }
</script>
