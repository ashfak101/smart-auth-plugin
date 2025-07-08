import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAppHelper } from './appHelper'
import AlertMessage from './AlertMessage'
import { loader } from './Loader'
import { loadStripe } from '@stripe/stripe-js'

export const useMembershipHelper = () => {
    const router = useRouter()
    const loading = ref(false)
    const { get, post } = useAppHelper()
    const { error, success } = AlertMessage()
    const selectedDuration = ref('')
    const { startLoading, stopLoading } = loader()
    const app_vars = window.smart_auth_app_vars
    const payment_gateway = ref(app_vars.payment_gateway)
    const stripe_public_key = ref(app_vars.stripe_public_key)
    const membership_plans = ref([])
    const selectedPlan = ref({})
    const stripePromise = loadStripe(stripe_public_key.value)
    const stripe = ref(null)
    const cardNumber = ref(null)
    const cardExpiry = ref(null)
    const cardCvc = ref(null)
    const isCardValid = ref(false)
    const isExpiryValid = ref(false)
    const isCvcValid = ref(false)
    const userRef = ref(null)
    const membershipForm = ref({
        membership_plan: 'Digital Membership',
        duration: '12 months',
        amount: 0,
        payment_gateway: 'Stripe',
    })
    const membershipFormRef = ref(null)

    const rules = {
        membership_plan: [
            {
                required: true,
                message: 'Please select a membership plan',
                trigger: 'blur',
            },
        ],
        duration: [
            {
                required: true,
                message: 'Please select a duration',
                trigger: 'blur',
            },
        ],
        amount: [
            {
                required: true,
                message: 'Please enter the amount',
                trigger: 'blur',
            },
        ],
    }

    const selectPlan = (plan) => {
        membershipForm.value.membership_plan = plan.type
        membershipForm.value.amount = plan.yearly_amount
        selectedPlan.value = plan
    }

    const getPaymentGetWay = () => {
        if (payment_gateway.value.includes('Stripe')) {
            return [{
                id: 'Stripe',
                title: 'Debit or Credit Card',
            }]
        } else if (payment_gateway.value.includes('PayPal')) {
            return [{
                id: 'PayPal',
                title: 'PayPal',
            }]
        } else {
            return [
                {
                    id: 'Stripe',
                    title: 'Debit or Credit Card',
                },
                {
                    id: 'PayPal',
                    title: 'PayPal',
                },
            ]
        }
    }

    const getDurationLabel = (duration) => {
        switch (duration) {
            case '12 months':
                return selectedPlan.value
                    ? '12 months (' +
                    selectedPlan.value.currency +
                    selectedPlan.value.yearly_amount +
                    ')'
                    : '12 months (10)'
            case '24 months':
                return selectedPlan.value
                    ? '24 months - 2 years XO Student Discount Digital Membership(' +
                    selectedPlan.value.currency +
                    selectedPlan.value.biennial_amount +
                    ')'
                    : '12 months (10)'
            default:
                return ''
        }
    }

    const handleDurationChange = (value) => {
        switch (value) {
            case '12 months':
                membershipForm.value.amount = selectedPlan.value.yearly_amount
            case '24 months':
                membershipForm.value.amount = selectedPlan.value.biennial_amount
            default:
                return
        }
    }

    const handleGatewayChange = (value) => {
        if (value === 'Stripe') {
            initStripeForm()
        } else {
            initPayPalButtons()
        }
    }

    // Initialize PayPal buttons
    const initPayPalButtons = async () => {
        //init Paypal Button
        paypal.Buttons({
            createOrder: (data, actions) => {
                return actions.order.create({
                    purchase_units: [
                        {
                            amount: {
                                value: parseInt(membershipForm.value.amount),
                            },
                            currency_code: selectedPlan.value.currency_name,
                        },
                    ],
                })
            },
            onApprove: (data, actions) => {
                return actions.order.capture().then(async (details) => {

                    const capture_id = details.purchase_units[0]?.payments?.captures[0]?.id

                    const paymentData = {
                        membership_plan: membershipForm.value.membership_plan,
                        duration: membershipForm.value.duration,
                        amount: membershipForm.value.amount,
                        customer_id: details.payer?.payer_id,
                        payment_id: details.id,
                        payment_gateway: 'PayPal',
                        paypal_capture_id: capture_id,
                    }
                    const response = await post('save-membership', paymentData)

                    if (response.status === 'success') {
                        success(response.message)
                        setTimeout(() => {
                            router.push(app_vars.profile_page)
                            window.location.replace(app_vars.profile_page)
                        }, 2000)
                    } else {
                        error(response.message)
                    }
                })
            },
            onError: (err) => {
                console.error(err)
                // Handle errors
            },
        }).render('#paypal-button-container')
    }

    // Initialize Stripe form
    const initStripeForm = async () => {
        stripe.value = await stripePromise
        const elements = stripe.value.elements()

        // Create separate elements for each field
        cardNumber.value = elements.create('cardNumber')
        cardExpiry.value = elements.create('cardExpiry')
        cardCvc.value = elements.create('cardCvc')

        // Mount elements
        cardNumber.value.mount('#card-number')
        cardExpiry.value.mount('#card-expiry')
        cardCvc.value.mount('#card-cvc')

        // Add listeners for error handling
        const fields = [
            { element: cardNumber.value, name: 'Card number' },
            { element: cardExpiry.value, name: 'Expiration date' },
            { element: cardCvc.value, name: 'CVC' },
        ]

        // Add listeners for validation
        cardNumber.value.on('change', (event) => {
            isCardValid.value = event.complete
            if (event.error) {
                error.value = `Card number: ${event.error.message}`
            } else {
                error.value = ''
            }
        })

        cardExpiry.value.on('change', (event) => {
            isExpiryValid.value = event.complete
            if (event.error) {
                error.value = `Expiration date: ${event.error.message}`
            } else if (!error.value.includes('Card number:')) {
                error.value = ''
            }
        })

        cardCvc.value.on('change', (event) => {
            isCvcValid.value = event.complete
            if (event.error) {
                error.value = `CVC: ${event.error.message}`
            } else if (
                !error.value.includes('Card number:') &&
                !error.value.includes('Expiration date:')
            ) {
                error.value = ''
            }
        })
        fields.forEach(({ element, name: fieldName }) => {
            element.addEventListener('change', (event) => {
                if (event.error) {
                    error.value = `${fieldName}: ${event.error.message}`
                } else {
                    error.value = ''
                }
            })
        })
    }

    // Watch for changes in payment gateway
    watch(() => membershipForm.value.payment_gateway, (newValue) => {
        if (newValue === 'PayPal') {
            initPayPalButtons()
        } else {
            initStripeForm()
        }
    })

    // Handle payment submission

    const handlePayment = async () => {
        membershipFormRef.value.validate((validData) => {
            if (validData) {
                handlePaymentSubmission()
            } else {
                return false
            }
        })
    }

    // Handle Stripe payment submission
    const handlePaymentSubmission = async () => {
        const stripe = await stripePromise
        const formData = new FormData()
        formData.append('amount', parseInt(membershipForm.value.amount) * 100)
        formData.append('currency', selectedPlan.value.currency_Name)
        formData.append('name', userRef.value.name)
        formData.append('email', userRef.value.email)
        loading.value = true
        try {
            const response = await post('create-payment-intent', formData)

            if (response.status !== 'success') {
                error(response.message || 'Something went wrong')
                return
            }

            const clientSecret = response.client_secret
            const customer_id = response.customer_id

            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardNumber.value,
                },
            })

            if (result.error) {

                error(result.error.message)
            } else if (result.paymentIntent.status === 'succeeded') {
                let paymentData = {
                    membership_plan: membershipForm.value.membership_plan,
                    duration: membershipForm.value.duration,
                    amount: membershipForm.value.amount,
                    customer_id: customer_id,
                    payment_id: result.paymentIntent.id,
                    payment_gateway: 'Stripe',
                }

                const response = await post('save-membership', paymentData)

                if (response.status === 'success') {
                    success(response.message)
                    setTimeout(() => {
                        router.push(app_vars.profile_page)
                        window.location.replace(app_vars.profile_page)
                    }, 2000)
                } else {
                    error(response.message)
                }
            }
        }
        catch (error) {
            error('Something went wrong')
        }
        finally {
            loading.value = false
        }
    }

    const getIconComponent = (type) => {
        return type === 'Digital Membership' ? 'CrossIcon' : 'IconCheckmark'
    }

    const showThis = (gateway) => {
        return membershipForm.value.payment_gateway.includes(gateway)
    }

    const fetchSettings = async () => {
        startLoading()
        const response = await get('get-membership-settings')
        if (response.status === 'success') {
            membership_plans.value = response.data.membership_plan
            userRef.value = response.data.user
            selectPlan(membership_plans.value[0])
            //membershipForm.payment_gateway.value = payment_gateway.value;
        } else {
            error(response.message)
        }
        stopLoading()
    }
    // for validation check and button enable/disable
    const isFormValid = () => {
        return isCardValid.value && isExpiryValid.value && isCvcValid.value
    }

    // Fetch data on mount
    onMounted(async () => {
        fetchSettings()
        initStripeForm()
    })

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
    }
}