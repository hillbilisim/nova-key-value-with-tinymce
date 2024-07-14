<template>
    <div v-if="isNotObject" class="flex items-center key-value-item">

        <div class="flex flex-grow border-b border-gray-200 dark:border-gray-700 key-value-fields">
            <div class="flex-none w-48 cursor-text" :class="[
          readOnlyKeys || !isEditable
            ? 'bg-gray-50 dark:bg-gray-800'
            : 'bg-white dark:bg-gray-900',]">
        <textarea
            rows="1"
            :dusk="`tiny-key-value-key-${index}`"
            v-model="item.key"
            @focus="handleKeyFieldFocus"
            ref="keyField"
            type="text"
            class="font-mono text-xs resize-none block w-full px-3 py-3 dark:text-gray-400 bg-clip-border focus:outline-none focus:ring focus:ring-inset"
            :readonly="!isEditable || readOnlyKeys"
            :tabindex="!isEditable || readOnlyKeys ? -1 : 0"
            style="background-clip: border-box"
            :class="{
            'bg-white dark:bg-gray-800 focus:outline-none cursor-not-allowed':
              !isEditable || readOnlyKeys,
            'hover:bg-20 focus:bg-white dark:bg-gray-900 dark:focus:bg-gray-900':
              isEditable && !readOnlyKeys,
          }"
        />
            </div>

            <div v-if="isEditable" class="flex-grow border-l border-gray-200 dark:border-gray-700">
                <editor :id="item.id"
                        v-model="item.value"
                        ref="valueField"
                        class="font-mono text-xs block w-full px-3 py-3 dark:text-gray-400"
                        :disabled="!isEditable"
                        :init="options"
                ></editor>


            </div>
            <div v-if="!isEditable" v-html="item.value"  class="flex-grow border-l border-gray-200 dark:border-gray-700">
            </div>
        </div>

        <div
            v-if="isEditable && canDeleteRow"
            class="flex justify-center h-11 w-11 absolute -right-[50px]"
        >
            <BasicButton
                @click="$emit('remove-row', item.id)"
                :dusk="`remove-key-value-${index}`"
                type="button"
                tabindex="0"
                class="flex items-center appearance-none cursor-pointer text-red-500 hover:text-red-600 active:outline-none active:ring focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600"
                :title="__('Delete')"
            >
                <Icon type="minus-circle"/>
            </BasicButton>
        </div>
    </div>
</template>

<script>
import autosize from 'autosize'
import Editor from '@tinymce/tinymce-vue'

export default {
    emits: ['remove-row'],
    components: {Editor},

    props: {
        index: Number,
        item: Object,
        disabled: {
            type: Boolean,
            default: false,
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
        readOnlyKeys: {
            type: Boolean,
            default: false,
        },
        canDeleteRow: {
            type: Boolean,
            default: true,
        },
    },

    mounted() {
        autosize(this.$refs.keyField)
        autosize(this.$refs.valueField)
    },

    methods: {
        handleKeyFieldFocus() {
            this.$refs.keyField.select()
        },
        options() {
            let options = this.field.options

            if (options.use_lfm) {
                options['file_picker_callback'] = this.filePicker
            }

            if (options.use_dark && options.content_css_dark && options.skin_url_dark) {
                if (document.documentElement.classList.contains('dark')) {
                    options['content_css'] = options['content_css_dark']
                    options['skin_url'] = options['skin_url_dark']
                }
            }

            return options
        },
        handleValueFieldFocus() {
            this.$refs.valueField.select()
        },
    },

    computed: {

        isNotObject() {
            return !(this.item.value instanceof Object)
        },
        isEditable() {
            return !this.readOnly && !this.disabled
        },
    },
}
</script>
