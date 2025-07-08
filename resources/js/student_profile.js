import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import { createApp } from 'vue';
import router from './router/StudentProfile';

import Student from './components/Student/Student.vue';
const app = createApp(Student );

app.use(router);
app.use(ElementPlus);

app.mount('#student_profile_all_tabs');