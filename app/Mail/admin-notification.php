<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Purchase Order Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2E6668, #87CFC3);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .order-details {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #ffffff;
        }
        .order-table th, .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .order-table th {
            background-color: #87CFC3;
            color: white;
        }
        .footer {
            background-color: #2E6668;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        @media only screen and (max-width: 600px) {
            table {
                width: 100% !important;
            }
            .content {
                padding: 10px !important;
            }
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td class="header">
                New Purchase Order Notification
                <p style="font-size: 14px; margin: 5px 0;">Order <?php echo  esc_html( $payment_id ); ?></p>
            </td>
        </tr>
        <tr>
            <td class="content">
                <p>Dear Admin,</p>
                <p>A new purchase order has been placed. Below are the order details:</p>
                <div class="order-details">
                    <p><strong>Order Date:</strong> <?php echo  esc_html( $order_date ); ?></p>
                    <p><strong>Purchase Order Number:</strong><?php echo  esc_html( $payment_id ); ?></p>
                    <p><strong>Customer Name:</strong> <?php echo  esc_html( $user_name ); ?></p>
                    <p><strong>Email:</strong> <?php echo  esc_html( $user_email ); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo  esc_html( $payment_method ); ?></p>
                </div>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo  esc_html( $item_name ); ?></td>
                            <td>1</td>
                            <td><?php echo  esc_html( $item_amount ); ?></td>
                            <td><?php echo  esc_html( $item_amount ); ?></td>
                        </tr>
                    </tbody>

                </table>
                <p>Please review this order and process it accordingly.</p>
                <p>Best regards,<br>Yo Card Admin Team</p>
            </td>
        </tr>
        <tr>
            <td class="footer">
            <p><svg width="90" height="48" viewBox="0 0 90 48" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M65.4677 47.9999C59.9152 48.0001 54.5344 46.0751 50.2421 42.5527C45.9498 39.0304 43.0116 34.1288 41.9282 28.683C40.8447 23.2372 41.6831 17.5842 44.3003 12.6872C46.9176 7.7902 51.1519 3.95222 56.2817 1.82719C61.4115 -0.297837 67.1194 -0.578427 72.4329 1.03322C77.7464 2.64487 82.3367 6.04904 85.4216 10.6657C88.5066 15.2824 89.8953 20.8259 89.3512 26.3517C88.8072 31.8775 86.3639 37.0437 82.4378 40.97C80.2144 43.2056 77.5698 44.9781 74.6569 46.1847C71.744 47.3914 68.6207 48.0084 65.4677 47.9999ZM65.4677 6.06315C55.5772 6.06315 47.5309 14.1095 47.5309 24C47.5309 33.8905 55.5772 41.9368 65.4677 41.9368C75.3582 41.9368 83.4045 33.8896 83.4045 24C83.4045 14.1103 75.3574 6.06315 65.4677 6.06315Z" fill="#66AFB5"/>
<path d="M39.1041 14.3609C39.1042 12.8798 38.9347 11.4035 38.5988 9.96094H31.5706C32.0988 11.3677 32.3687 12.8583 32.3673 14.3609C32.3673 21.2797 26.7386 26.9083 19.8199 26.9083C12.9012 26.9083 7.27256 21.2797 7.27256 14.3609C7.27143 12.8582 7.54158 11.3677 8.07003 9.96094H1.03847C0.703007 11.4036 0.533487 12.8798 0.533203 14.3609C0.534027 18.8915 2.1292 23.2773 5.0392 26.7497C7.94919 30.2221 11.9884 32.5597 16.449 33.3529V47.1777H23.1858V33.3529C27.6464 32.5597 31.6856 30.2221 34.5956 26.7497C37.5056 23.2773 39.1007 18.8915 39.1016 14.3609H39.1041Z" fill="#66AFB5"/>
</svg>
</p>
                <p>YoCard Inc.<br>456 Business Avenue, Suite 789<br>San Francisco, CA 94105<br><a href="mailto:info@techcorp.com" style="color: #ffffff;">info@techcorp.com</a></p>
                <p>This email was sent to <strong>john.smith@email.com</strong>. Please do not reply.</p>
            </td>
        </tr>
    </table>
</body>
</html>
