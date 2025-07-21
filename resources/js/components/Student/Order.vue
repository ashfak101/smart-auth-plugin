<template>
  <div class="order-page">
    <el-container class="full-height">
      <el-main class="main-center">
        <Menu />
        <div class="order-container">
          <h2 class="order-title">My Orders</h2>
          <el-card shadow="hover" class="order-card">
            <el-table
              v-if="orders.length > 0"
              :data="orders"
              border
              style="width: 100%">
              <el-table-column prop="id" label="Order ID" />
              <el-table-column prop="date" label="Date" />
              <el-table-column label="Status">
                <template #default="scope">
                  <span
                    :class="['status-badge', getStatusClass(scope.row.status)]">
                    {{ scope.row.status }}
                  </span>
                </template>
              </el-table-column>
              <el-table-column prop="total" label="Total Amount" />
              <el-table-column label="Actions">
                <template #default="scope">
                  <el-button
                    type="primary"
                    size="small"
                    class="view-order-button"
                    @click="viewOrderDetails(scope.row.id)"
                    >View</el-button
                  >
                </template>
              </el-table-column>
            </el-table>
            <div v-else class="no-orders">
              <p>No orders found.</p>
            </div>
          </el-card>
        </div>
      </el-main>
    </el-container>

    <!-- Order Details Modal -->
    <el-dialog
      title="Order Details"
      v-model="orderDetailsVisible"

      width="600px">
      <div v-if="orderDetails">
        <p><strong>Order ID:</strong> {{ orderDetails.id }}</p>
        <p><strong>Date:</strong> {{ orderDetails.date }}</p>
        <p><strong>Status:</strong> {{ orderDetails.status }}</p>
        <p>
          <strong>Total:</strong> {{ orderDetails.total }}
          {{ orderDetails.currency }}
        </p>
        <h4>Items:</h4>
        <el-table :data="orderDetails.items" border style="width: 100%">
          <el-table-column prop="product_name" label="Product Name" />
          <el-table-column prop="quantity" label="Quantity" />
          <el-table-column prop="price" label="Price" />
        </el-table>
      </div>
      <div v-else>
        <p>Loading order details...</p>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="orderDetailsVisible = false">Close</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
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
        const response = await get(`get_single_order_details?order_id=${orderId}`);
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
    });

    return {
      orders,
      orderDetails,
      orderDetailsVisible,
      viewOrderDetails,
      getStatusClass,
    };
  },
};
</script>

<style scoped>
.order-page {
  padding: 20px;
}

.order-container {
  max-width: 1200px;
  margin: 0 auto;
}

.order-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
  text-align: center;
}

.order-card {
  border-radius: 8px;
  background-color: #f9f9f9;
}

.no-orders {
  text-align: center;
  font-size: 16px;
  color: #999;
  padding: 20px;
}

/* Status Badge Styles */
.status-badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
  text-transform: capitalize;
  color: #fff;
}

.status-completed {
  background-color: #67c23a; /* Green */
}

.status-pending {
  background-color: #e6a23c; /* Orange */
}

.status-cancelled {
  background-color: #f56c6c; /* Red */
}

.status-default {
  background-color: #909399; /* Gray */
}
.view-order-button{
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
}
.view-order-button:hover {
    background-color: #555;
}
</style>