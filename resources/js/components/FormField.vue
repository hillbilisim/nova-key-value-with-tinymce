<template>
  <PanelItem :index="index" :field="field">
    <template #value> Test
      <TinyKeyValueTable v-if="theData.length > 0" :edit-mode="false" class="overflow-hidden">
        <TinyKeyValueHeader :key-label="field.keyLabel" :value-label="field.valueLabel"/>

        <div class="bg-gray-50 dark:bg-gray-700 overflow-hidden key-value-items">
          <TinyKeyValueItem
              v-for="(item, index) in theData"
              :index="index"
              :item="item"
              :disabled="true"
              :key="item.key"
              :class="errorClasses"
              :placeholder="field.name"
              :init="options"
          />
        </div>
      </TinyKeyValueTable>
    </template>
  </PanelItem>
</template>

<script>
import map from 'lodash/map'
import {DependentFormField, HandlesValidationErrors} from 'laravel-nova'
import Editor from '@tinymce/tinymce-vue'

export default {
  components: {Editor},

  mixins: [DependentFormField, HandlesValidationErrors],

  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
  computed: {
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
    }
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || ''
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.value || '')
    },

    /**
     * Update the field's internal value.
     */
    handleChange(value) {
      this.value = value
    },

    filePicker: function (callback, value, meta) {
      let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

      let type = 'image' === meta.filetype ? 'Images' : 'Files';
      let url = this.options.path_absolute + this.options.lfm_url + '?editor=tinymce5&type=' + type;

      tinymce.activeEditor.windowManager.openUrl({
        url: url,
        title: 'File manager',
        width: x * 0.8,
        height: y * 0.8,
        onMessage: (api, message) => {
          callback(message.content);
        }
      });
    }
  },
  data: () => ({theData: []}),

  created() {
    this.theData = map(
        Object.entries(this.field.value || {}),
        ([key, value]) => ({
          key: `${key}`,
          value,
        })
    )
  },
}
</script>