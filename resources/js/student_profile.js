import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import { createApp } from 'vue';
import router from './router/StudentProfile';
import MyAccounts from './components/Student/MyAccounts.vue'


const app = createApp(MyAccounts );

app.use(router);
app.use(ElementPlus);

app.mount('#my_profile_all_tabs');