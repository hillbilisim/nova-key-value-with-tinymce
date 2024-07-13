<template>
  <DefaultField
      :field="currentField"
      :errors="errors"
      :full-width-content="['modal', 'action-modal'].includes(mode)"
      :show-help-text="showHelpText"
  >
    <template #field>
      <TinyKeyValueTable
          :edit-mode="!currentlyIsReadonly"
          :can-delete-row="currentField.canDeleteRow"
      >
        <KeyValueHeader
            :key-label="currentField.keyLabel"
            :value-label="currentField.valueLabel"
        />

        <div class="bg-white dark:bg-gray-800 overflow-hidden key-value-items">
          <KeyValueItem
              v-for="(item, index) in theData"
              :index="index"
              @remove-row="removeRow"
              :item.sync="item"
              :key="item.id"
              :ref="item.id"
              :read-only="currentlyIsReadonly"
              :read-only-keys="currentField.readonlyKeys"
              :can-delete-row="currentField.canDeleteRow"
          />
        </div>
      </TinyKeyValueTable>

      <div
          class="mr-11"
          v-if="
          !currentlyIsReadonly &&
          !currentField.readonlyKeys &&
          currentField.canAddRow
        "
      >
        <button
            @click="addRowAndSelect"
            :dusk="`${field.attribute}-add-key-value`"
            type="button"
            class="cursor-pointer focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 focus:ring-offset-4 dark:focus:ring-offset-gray-800 rounded-lg mx-auto text-primary-500 font-bold link-default mt-3 px-3 rounded-b-lg flex items-center"
        >
          <Icon type="plus-circle" />
          <span class="ml-1">{{ currentField.actionText }}</span>
        </button>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import map from 'lodash/map'
 import Editor from '@tinymce/tinymce-vue'
import findIndex from 'lodash/findIndex'
import fromPairs from 'lodash/fromPairs'
import reject from 'lodash/reject'
import tap from 'lodash/tap'
import TinyKeyValueTable from "../Form/TinyKeyValueTable";

function guid() {
  var S4 = function () {
    return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1)
  }
  return (
      S4() +
      S4() +
      '-' +
      S4() +
      '-' +
      S4() +
      '-' +
      S4() +
      '-' +
      S4() +
      S4() +
      S4()
  )
}

export default {
  components: {Editor,TinyKeyValueTable},


  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    finalPayload() {
      return fromPairs(
          reject(
              map(this.theData, row =>
                  row && row.key ? [row.key, row.value] : undefined
              ),
              row => row === undefined
          )
      )
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


    /**
     * Update the field's internal value.
     */
    handleChange(value) {
      this.value = value
    },
    populateKeyValueData() {
      this.theData = map(Object.entries(this.value || {}), ([key, value]) => ({
        id: guid(),
        key: `${key}`,
        value,
      }))

      if (this.theData.length == 0) {
        this.addRow()
      }
    },
    fill(formData) {
      this.fillIfVisible(
          formData,
          this.field.attribute,
          JSON.stringify(this.finalPayload)
      )
    },
    addRow() {
      return tap(guid(), id => {
        this.theData = [...this.theData, { id, key: '', value: '' }]
        return id
      })
    },

    /**
     * Add a row to the table and select its first field.
     */
    addRowAndSelect() {
      return this.selectRow(this.addRow())
    },

    /**
     * Remove the row from the table.
     */
    removeRow(id) {
      return tap(
          findIndex(this.theData, row => row.id == id),
          index => this.theData.splice(index, 1)
      )
    },

    /**
     * Select the first field in a row with the given ref ID.
     */
    selectRow(refId) {
      return this.$nextTick(() => {
        this.$refs[refId][0].handleKeyFieldFocus()
      })
    },

    onSyncedField() {
      this.populateKeyValueData()
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
  mounted() {
    this.populateKeyValueData()
  },
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