import Images from './components/Images'
import ImageUploadField from './components/ImageUploadField'

App.booting((Vue, router) => {
    Vue.component('images-tool', Images)
    Vue.component('image-upload-field', ImageUploadField)
})