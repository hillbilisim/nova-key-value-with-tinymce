import IndexNovaTinyMCEField from './components/IndexField';
import DetailNovaTinyMCEField from './components/DetailField';
import FormNovaTinyMCEField from './components/FormField';
import FormTinyKeyValueHeader from './Form/TinyKeyValueHeader.vue';
import FormTinyKeyValueItem from './Form/TinyKeyValueItem.vue';
import FormTinyKeyValueTable from './Form/TinyKeyValueTable.vue';

Nova.booting((app, store) => {
    app.component('index-Nova-KeyValueTinyMCE', IndexNovaTinyMCEField);
    app.component('detail-Nova-KeyValueTinyMCE', DetailNovaTinyMCEField);
    app.component('form-Nova-KeyValueTinyMCE', FormNovaTinyMCEField);
    app.component('FormTinyKeyValueHeader', FormTinyKeyValueHeader);
    app.component('FormTinyKeyValueItem', FormTinyKeyValueItem);
    app.component('FormTinyKeyValueTable', FormTinyKeyValueTable);
})
