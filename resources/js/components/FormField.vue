<template>

  <TinyKeyValueTable
      :edit-mode="!currentlyIsReadonly"
      :can-delete-row="theData.canDeleteRow"
  >
    <KeyValueHeader
        :key-label="theData.keyLabel"
        :value-label="theData.valueLabel"
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
          :read-only-keys="theData.readonlyKeys"
          :can-delete-row="theData.canDeleteRow"
      />
    </div>
  </TinyKeyValueTable>

  <div
      class="mr-11"
      v-if="
          !currentlyIsReadonly &&
          !theData.readonlyKeys &&
          theData.canAddRow
        "
  >
    <button
        @click="addRowAndSelect"
        :dusk="`${field.attribute}-add-key-value`"
        type="button"
        class="cursor-pointer focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 focus:ring-offset-4 dark:focus:ring-offset-gray-800 rounded-lg mx-auto text-primary-500 font-bold link-default mt-3 px-3 rounded-b-lg flex items-center"
    >
      <Icon type="plus-circle"/>
      <span class="ml-1">{{ theData.actionText }}</span>
    </button>
  </div>
</template>

<script>
import map from 'lodash/map'
import Editor from '@tinymce/tinymce-vue'
import TinyKeyValueTable from "../Form/TinyKeyValueTable.vue";
import tap from "lodash/tap";
import findIndex from "lodash/findIndex";
import fromPairs from "lodash/fromPairs";
import reject from "lodash/reject";

export default {
  components: {Editor, TinyKeyValueTable},


  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    /**
     * Return the final filtered json object
     */
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

    mounted() {
      this.populateKeyValueData()
    },

    methods: {
      /*
       * Set the initial value for the field
       */
      populateKeyValueData() {
        this.theData = map(Object.entries(this.value || {}), ([key, value]) => ({
          id: guid(),
          key: `${key}`,
          value,
        }))

        if (this.theData.length == 0) {
          this.addRow()
        }
        console.log(this.theData);
      },

      /**
       * Provide a function that fills a passed FormData object with the
       * field's internal value attribute.
       */
      fill(formData) {
        this.fillIfVisible(
            formData,
            this.field.attribute,
            JSON.stringify(this.finalPayload)
        )
      },

      /**
       * Add a row to the table.
       */
      addRow() {
        return tap(guid(), id => {
          this.theData = [...this.theData, {id, key: '', value: ''}]
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