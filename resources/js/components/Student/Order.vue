<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <div class="order-container">
            <h2 class="order-title">My Orders</h2>
            <el-card shadow="hover" class="order-card">
              <!-- Add responsive table wrapper -->
              <div class="table-responsive">
                <el-table
                  v-if="orders.length > 0"
                  :data="orders"
                  border
                  style="width: 100%">
                  <el-table-column prop="id" label="Order ID" min-width="100" />
                  <el-table-column prop="date" label="Date" min-width="150" />
                  <el-table-column label="Status" min-width="120">
                    <template #default="scope">
                      <span
                        :class="[
                          'status-badge',
                          getStatusClass(scope.row.status),
                        ]">
                        {{ scope.row.status }}
                      </span>
                    </template>
                  </el-table-column>
                  <el-table-column
                    prop="total"
                    label="Total Amount"
                    min-width="120" />
                  <el-table-column
                    label="Actions"
                    min-width="100"
                    fixed="right">
                    <template #default="scope">
                      <el-button
                        type="primary"
                        size="small"
                        class="view-order-button"
                        @click="viewOrderDetails(scope.row.id)"
                        >View</el-button
                      >
                      <!-- <el-button
                        type="success"

                        @click="downloadInvoice(scope.row.id)">
                        Download Invoice
                      </el-button> -->
                    </template>
                  </el-table-column>
                </el-table>
                <div v-else class="no-orders">
                  <p>No orders found.</p>
                </div>
              </div>
            </el-card>
          </div>
        </div>
      </el-main>
    </el-container>

    <!-- Order Details Modal -->
    <el-dialog
      title="Order Details"
      v-model="orderDetailsVisible"
      :fullscreen="isMobile"
      :width="isMobile ? '100%' : '600px'"
      class="order-details-dialog">
      <div v-if="orderDetails" class="order-details-content">
        <div class="order-info">
          <p><strong>Order ID:</strong> {{ orderDetails.order_id }}</p>
          <p><strong>Date:</strong> {{ orderDetails.date_created }}</p>
          <p><strong>Status:</strong> {{ orderDetails.status }}</p>
          <p>
            <strong>Total:</strong> {{ orderDetails.total }}
            {{ orderDetails.currency }}
          </p>
        </div>
        <h4>Items:</h4>
        <div class="table-responsive">
          <el-table :data="orderDetails.items" border style="width: 100%">
            <el-table-column
              prop="product_name"
              label="Product Name"
              min-width="150" />
            <el-table-column prop="quantity" label="Quantity" min-width="100" />
            <el-table-column prop="price" label="Price" min-width="100" />
          </el-table>
        </div>
      </div>
      <div v-else>
        <p>Loading order details...</p>
      </div>
      <template #footer>
        <el-button @click="orderDetailsVisible = false">Close</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { onMounted, ref, onUnmounted } from 'vue';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';
import Menu from './Menu.vue';

export default {
  name: 'Order',
  components: {
    Menu,
  },
  setup() {
    const { get } = useAppHelper();
    const { error } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const app_vars = window.smart_auth_app_vars;
    const id = app_vars.user_id;
    const orders = ref([]);
    const orderDetails = ref(null);
    const orderDetailsVisible = ref(false);

    // Add responsive detection
    const isMobile = ref(window.innerWidth <= 768);

    const handleResize = () => {
      isMobile.value = window.innerWidth <= 768;
    };

    // Fetch all orders for the user
    const fetchOrders = async () => {
      startLoading();
      try {
        // Pass user_id as a query parameter
        const response = await get(`get_orders_by_user_id?user_id=${id}`);
        if (response.status === 'success') {
          orders.value = response.data || [];
        } else {
          error(response.message || 'Failed to fetch orders.');
        }
      } catch (err) {
        console.error('Error fetching orders:', err);
        error('Error fetching orders. Please try again later.');
      } finally {
        stopLoading();
      }
    };

    // Fetch details for a single order
    const viewOrderDetails = async (orderId) => {
      orderDetailsVisible.value = true;
      console.log('Fetching details for order ID:', orderDetailsVisible.value);
      orderDetails.value = null; // Clear previous details
      //   startLoading();
      try {
        // Pass order_id as a query parameter
        const response = await get(
          `get_single_order_details?order_id=${orderId}`
        );
        if (response.status === 'success') {
          orderDetails.value = response.data;
        } else {
          error(response.message || 'Failed to fetch order details.');
        }
      } catch (err) {
        console.error('Error fetching order details:', err);
        error('Error fetching order details. Please try again later.');
      } finally {
        // stopLoading();
      }
    };

    // Get CSS class for order status
    const getStatusClass = (status) => {
      switch (status.toLowerCase()) {
        case 'on-hold':
          return 'status-default';
        case 'processing':
          return 'status-pending';
        case 'cancelled':
          return 'status-cancelled';
        case 'completed':
          return 'status-completed';
        default:
          return 'status-default';
      }
    };

    onMounted(() => {
      fetchOrders();
      window.addEventListener('resize', handleResize);
    });

    onUnmounted(() => {
      window.removeEventListener('resize', handleResize);
    });
    const downloadInvoice = (orderId) => {
      if (!orderId) return;
      // Open the invoice download API in a new tab/window
    //   window.open(
    //     `/wp-json/smart-auth/v2/download_order_invoice?order_id=${orderId}`,
    //     '_blank'
    //   );
    };
    return {
      orders,
      orderDetails,
      orderDetailsVisible,
      viewOrderDetails,
      getStatusClass,
      downloadInvoice,
      isMobile,
    };
  },
};
</script>

<style scoped>
/* Base styles */
.order-page {
  padding: 20px;
}

.dashboard-container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

.order-container {
  width: 100%;
}

/* Responsive table wrapper */
.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* Card styles */
.order-card {
  border-radius: 8px;
  background-color: #f9f9f9;
  margin: 15px 0;
}

/* Title styles */
.order-title {
  font-size: clamp(20px, 4vw, 24px);
  font-weight: bold;
  margin-bottom: 20px;
  text-align: center;
}

/* Status badge styles */
.status-badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
  text-transform: capitalize;
  color: #fff;
  white-space: nowrap;
}

/* Button styles */
.view-order-button {
  background-color: #333;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 5px 10px;
  cursor: pointer;
  white-space: nowrap;
}

.view-order-button:hover {
  background-color: #555;
}

/* Order details dialog */
.order-details-dialog {
  max-height: 90vh;
}

.order-details-content {
  padding: 15px;
}

.order-info {
  margin-bottom: 20px;
}

/* Responsive styles */
@media screen and (max-width: 768px) {
  .dashboard-container {
    padding: 0 10px;
  }

  .order-card {
    margin: 10px 0;
  }

  .status-badge {
    padding: 3px 8px;
    font-size: 11px;
  }

  .order-details-content {
    padding: 10px;
  }

  /* Adjust table for mobile */
  :deep(.el-table) {
    font-size: 14px;
  }

  :deep(.el-table .cell) {
    padding: 8px;
  }
}

/* Status colors */
.status-completed {
  background-color: #67c23a;
}
.status-pending {
  background-color: #e6a23c;
}
.status-cancelled {
  background-color: #f56c6c;
}
.status-default {
  background-color: #909399;
}

/* Loading state */
.no-orders {
  text-align: center;
  font-size: 16px;
  color: #999;
  padding: 20px;
}
</style>
