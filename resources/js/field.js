import IndexNovaTinyMCEField from './components/IndexField';
import DetailNovaTinyMCEField from './components/DetailField';
import FormNovaTinyMCEField from './components/FormField';

Nova.booting((app, store) => {
    app.component('index-Nova-KeyValueTinyMCE', IndexNovaTinyMCEField);
    app.component('detail-Nova-KeyValueTinyMCE', DetailNovaTinyMCEField);
    app.component('form-Nova-KeyValueTinyMCE', FormNovaTinyMCEField);
})
