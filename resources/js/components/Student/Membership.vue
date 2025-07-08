<template>
  <div class="common-layout">
    <el-container class="full-height membership-container">
      <el-main class="main-center">
        <div class="dashboard-container">
          <h1 class="page-heading">Select Membership</h1>
          <el-form
            :model="membershipForm"
            ref="membershipFormRef"
            :rules="rules"
            label-position="top"
            label-width="auto"
            class="register-form"
            validate-on-blur
            validate-on-change>
            <div class="membership-plans">
              <div
                @click="selectPlan(plan)"
                class="box-card"
                v-for="plan in membership_plans"
                :key="plan.type"
                :class="{
                  'selected-plan': membershipForm.membership_plan === plan.type,
                }">
                <div class="plan-header-section">
                  <img :src="plan.image" :alt="plan.type" />
                  <div class="plan-type">
                    <h2>{{ plan.title }}</h2>
                    <span> {{ plan.type }}</span>
                  </div>
                </div>

                <div class="plan-details">
                  <div class="plan-features">
                    <IconCheckmark />
                    <span>Get access to 100’s of great deals</span>
                  </div>
                  <el-row :gutter="20" style="margin-bottom: 14px">
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Full wellness suite</span>
                      </div>
                    </el-col>
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Meditation videos</span>
                      </div>
                    </el-col>
                  </el-row>
                  <el-row :gutter="20" style="margin-bottom: 14px">
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Les mills workout videos</span>
                      </div>
                    </el-col>
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Mental health app</span>
                      </div>
                    </el-col>
                  </el-row>
                  <el-row :gutter="20" style="margin-bottom: 14px">
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Yoga sessions</span>
                      </div>
                    </el-col>
                    <el-col :span="12">
                      <div class="plan-features">
                        <component :is="getIconComponent(plan.type)" />
                        <span>Financial support</span>
                      </div>
                    </el-col>
                  </el-row>
                </div>
                <div class="plan-footer">
                  <h2>
                    <span v-html="plan.currency + plan.yearly_amount"></span
                    ><strong> /year</strong>
                  </h2>
                  <div class="plan-footer-month-section">
                    <div>
                      <svg
                        width="30"
                        height="22"
                        viewBox="0 0 30 22"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M29.9673 8.50377L27.1345 21.0853C27.0783 21.3486 26.9326 21.5839 26.7224 21.7508C26.5122 21.9177 26.2507 22.0057 25.9829 21.9997H3.91876C3.65096 22.0057 3.38947 21.9177 3.17928 21.7508C2.9691 21.5839 2.82337 21.3486 2.76719 21.0853L0.026443 8.50377C-0.0196818 8.29118 -0.00525363 8.06983 0.0680726 7.8651C0.141399 7.66038 0.270653 7.48058 0.441009 7.34632C0.611834 7.21158 0.81708 7.12813 1.03306 7.1056C1.24905 7.08308 1.46697 7.12239 1.66168 7.219L8.38686 10.4946L13.9605 0.586771C14.0609 0.408641 14.2066 0.260477 14.3826 0.157394C14.5586 0.0543119 14.7586 0 14.9623 0C15.1661 0 15.3661 0.0543119 15.5421 0.157394C15.7181 0.260477 15.8638 0.408641 15.9642 0.586771L21.5378 10.5062L28.3091 7.20742C28.5052 7.10294 28.7271 7.05764 28.9483 7.07698C29.1694 7.09632 29.3802 7.17948 29.5554 7.31644C29.7306 7.4534 29.8627 7.63833 29.9358 7.84898C30.0089 8.05963 30.0198 8.28701 29.9673 8.50377Z"
                          fill="#FFB743" />
                      </svg>
                      <span class="best-value"> Best value</span>
                    </div>
                    <div class="plan-footer-month">
                      <span
                        >24 months<span style="padding: 0; font-weight: 700"
                          >({{ plan.currency + plan.biennial_amount }})</span
                        >
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="payment-details">
              <h3 class="payment-title">Payment Details</h3>
              <div class="membership-duration">
                <el-radio-group
                  v-model="membershipForm.duration"
                  class="membership-duration-radio"
                  @change="handleDurationChange">
                  <el-radio
                    value="12 months"
                    :label="getDurationLabel('12 months')"
                    >{{ getDurationLabel('12 months') }}</el-radio
                  >
                  <el-radio
                    value="24 months"
                    :label="getDurationLabel('24 months')"
                    >{{ getDurationLabel('24 months') }}</el-radio
                  >
                </el-radio-group>

                <el-form-item label="Payment Getway" prop="payment_gateway">
                  <el-radio-group
                    v-model="membershipForm.payment_gateway"
                    @change="handleGatewayChange">
                    <!-- need to make this dynamic -->
                    <el-radio
                      v-for="item in getPaymentGetWay()"
                      :value="item.id"
                      :key="item.title"
                      :label="item.title"
                      >{{ item.title }}</el-radio
                    >
                  </el-radio-group>
                </el-form-item>

                <div class="stripe-form" v-if="showThis('Stripe')">
                  <div class="stripe-form-row-group">
                    <div class="stripe-form-row">
                      <label for="card-number">Card Number</label>
                      <div id="card-number" class="stripe-element"></div>
                    </div>
                    <div class="stripe-form-row">
                      <label for="card-expiry">Expiration Date</label>
                      <div id="card-expiry" class="stripe-element"></div>
                    </div>

                    <div class="stripe-form-row">
                      <label for="card-cvc">CVC</label>
                      <div id="card-cvc" class="stripe-element"></div>
                    </div>
                  </div>
                  <el-button
                    type="primary"
                    class="checkout-button"
                    @click="handlePayment"
                    :disabled="loading"
                    >{{
                      loading ? 'Processing...' : 'Make a Payment'
                    }}</el-button
                  >
                </div>
                <div class="paypal-form" v-if="showThis('PayPal')">
                  <div id="paypal-button-container"></div>
                </div>
              </div>
            </div>
          </el-form>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { useMembershipHelper } from '../../Composable/membershipHelper';
import IconCheckmark from './IconCheckmark.vue';
import CrossIcon from './CrossIcon.vue';

export default {
  name: 'Membership',
  components: {
    IconCheckmark,
    CrossIcon,
  },
  setup() {
    const {
      loading,
      membershipForm,
      membershipFormRef,
      rules,
      membership_plans,
      selectPlan,
      getIconComponent,
      selectedDuration,
      handlePayment,
      getDurationLabel,
      handleDurationChange,
      isFormValid,
      showThis,
      getPaymentGetWay,
      handleGatewayChange,
    } = useMembershipHelper();
    return {
      loading,
      membershipForm,
      membershipFormRef,
      rules,
      membership_plans,
      selectPlan,
      getIconComponent,
      selectedDuration,
      handlePayment,
      getDurationLabel,
      handleDurationChange,
      isFormValid,
      showThis,
      getPaymentGetWay,
      handleGatewayChange,
    };
  },
};
</script>
<style scoped>
.page-heading {
  font-size: 33px;
  font-weight: 400;
  color: #000000;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
