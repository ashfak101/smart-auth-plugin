<template>
    <div class="common-layout">
        <el-container class="full-height">
            <el-main class="main-center">
                <div class="dashboard-container">
                    <Menu />
                    <el-row :gutter="30" class="mt">
                        <el-card class="full-width-card">
                            <el-table :data="historyData" style="width: 100%">
                                <el-table-column prop="date" label="Date" />
                                <el-table-column prop="deal" label="Deal" />
                                <el-table-column align="right">
                                    <template #header>
                                        <el-input size="small" placeholder="Type to search" />
                                    </template>
                                    <template #default="scope">
                                        <a :href="scope.row.permalink" target="_blank"
                                            class="primary-button"><strong>Revisit</strong></a>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <el-pagination @current-change="handlePageChange" @size-change="handleSizeChange"
                                :current-page="currentPage" :page-size="pageSize" :background="true" :total="totalItems"
                                layout="total, sizes, prev, pager, next" />
                        </el-card>
                    </el-row>
                </div>
            </el-main>
        </el-container>
    </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from "../../Composable/AlertMessage";
import { loader } from '../../Composable/Loader';
import Menu from './Menu.vue';

export default {
    name: 'History',
    components: {
        Menu
    },
    setup() {
        const { get } = useAppHelper();
        const { error } = AlertMessage();
        const { startLoading, stopLoading } = loader();
        const currentPage = ref(1);
        const pageSize = ref(10);
        const totalItems = ref(0);
        const historyData = ref([]);

        const fetchHistory = async () => {
            startLoading();
            try {
                const response = await get(`get-history?page=${currentPage.value}&per_page=${pageSize.value}`);
                if (response) {
                    currentPage.value = response.current_page;
                    pageSize.value = response.per_page;
                    totalItems.value = parseInt(response.total_items);
                    historyData.value = response.history_data;
                }
            } catch (err) {
                error(err.message);
            } finally {
                stopLoading();
            }
        };

        const handlePageChange = (newPage) => {
            currentPage.value = newPage;
            fetchHistory();
        };

        const handleSizeChange = (newPageSize) => {
            pageSize.value = newPageSize;
            if (pageSize.value > totalItems.value) {
                currentPage.value = 1;
            }
            fetchHistory();
        };

        // Fetch data on mount
        onMounted(() => {
            fetchHistory();
        });

        return {
            currentPage,
            pageSize,
            totalItems,
            historyData,
            handlePageChange,
            handleSizeChange,
        };
    },
};
</script>
<style scoped>
.full-width-card {
    width: 100%;
}

.el-pagination {
    margin-top: 2%;
}

.btn {
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
}

.btn-primary {
    background-color: #409EFF;
    color: white;
}
</style>
