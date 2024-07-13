import IndexNovaTinyMCEField from './components/IndexField';
import DetailNovaTinyMCEField from './components/DetailField';
import FormNovaTinyMCEField from './components/FormField';
import FormTinyKeyValueHeader from './Form/TinyKeyValueHeader';
import FormTinyKeyValueItem from './Form/TinyKeyValueItem';
import FormTinyKeyValueTable from './Form/TinyKeyValueTable';

Nova.booting((app, store) => {
    app.component('index-Nova-KeyValueTinyMCE', IndexNovaTinyMCEField);
    app.component('detail-Nova-KeyValueTinyMCE', DetailNovaTinyMCEField);
    app.component('form-Nova-KeyValueTinyMCE', FormNovaTinyMCEField);
    app.component('TinyKeyValueHeader', FormTinyKeyValueHeader);
    app.component('TinyKeyValueItem', FormTinyKeyValueItem);
    app.component('TinyKeyValueTable', FormTinyKeyValueTable);
})
