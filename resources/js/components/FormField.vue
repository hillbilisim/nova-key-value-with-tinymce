<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      kv
      <TinyKeyValueTable
          v-if="theData.length > 0"
          :edit-mode="false"
          class="overflow-hidden"
      >
        <TinyKeyValueHeader
            :key-label="field.keyLabel"
            :value-label="field.valueLabel"
        />

        <div
            class="bg-gray-50 dark:bg-gray-700 overflow-hidden key-value-items"
        >
          <TinyKeyValueItem
              v-for="(item, index) in theData"
              :index="index"
              :item="item"
              :disabled="true"
              :key="item.key"
          />
        </div>
      </TinyKeyValueTable>
    </template>
  </PanelItem>

</template>

<script>
import map from 'lodash/map'
import Editor from '@tinymce/tinymce-vue'

export default {
  components: {Editor},


  data: () => ({theData: []}),

  created() {
    this.theData = map(
        Object.entries(this.field.value || {}),
        ([key, value]) => ({
          key: `${key}`,
          value,
        })
    )
    console.log(this.theData)
  },
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],


}
</script>