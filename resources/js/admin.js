import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import { createApp } from 'vue';
import router from './router';

import Admin from './components/Admin/Admin.vue';
const app = createApp( Admin );

app.use(router);
app.use(ElementPlus);

app.mount('#student_discount_card_admin_app');