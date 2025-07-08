<?php

namespace AK\SMARTAUTH\Http\Model;

defined('ABSPATH') || exit; // Prevent direct access

class OrderHistory extends Model
{
    protected $tableName = 'yo_card_order_history';

    public function get_order_row($customer_id, $payment_id)
    {
        $result = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "SELECT * FROM {$this->tableName} WHERE  customer_id = %s AND payment_id = %s",
                $customer_id,
                $payment_id
            )
        );

        return $result ? $result : null;
    }

    public function get_all_unique_user( $date = null )
    {
        if( $date ) {
            $date = date( 'Y-m', strtotime( $date ) );
            $result = $this->_wpdb->get_results(
                $this->_wpdb->prepare(
                    "SELECT DISTINCT user_id FROM {$this->tableName} WHERE DATE_FORMAT(created_at, %s) = %s",
                    '%Y-%m', $date
                )
            );
        } else {
            $result = $this->_wpdb->get_results(
                $this->_wpdb->prepare(
                    "SELECT DISTINCT user_id FROM {$this->tableName}"
                )
            );
        }

        return $result ? $result : [];
    }



    public function get_all_unique_active_user( $date = null )
    {
        if( $date ) {
            $date = date( 'Y-m', strtotime( $date ) );
            $result = $this->_wpdb->get_results(
                $this->_wpdb->prepare(
                    "SELECT DISTINCT user_id FROM {$this->tableName} WHERE DATE_FORMAT(created_at, %s) = %s AND refund_status != 'refunded'",
                    '%Y-%m', $date
                )
            );
        } else {
            $result = $this->_wpdb->get_results(
                $this->_wpdb->prepare(
                    "SELECT DISTINCT user_id FROM {$this->tableName} WHERE refund_status != 'refunded'"
                )
            );
        }

        return $result ? $result : [];
    }

    public function get_order_row_by_capture_id( $capture_id)
    {
        $result = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "SELECT * FROM {$this->tableName} WHERE paypal_capture_id = %s",
                $capture_id
            )
        );

        return $result ? $result : null;
    }

    public function search($query)
    {
        // Get all distinct user IDs with their emails
        $users = self::get_all_unique_user();
        $user_ids = [];

        foreach ($users as $user) {
            $user_info = get_userdata($user->user_id);
            if ($user_info && stripos($user_info->user_email, $query) !== false) {
                $user_ids[] = intval($user->user_id);
            }
        }

        // Base query string
        $query_string = "SELECT * FROM {$this->tableName} WHERE";

        // Append conditions
        $conditions = [];
        $params = [];

        if (!empty($user_ids)) {
            $placeholders = implode(',', array_fill(0, count($user_ids), '%d'));
            $conditions[] = "user_id IN ($placeholders)";
            $params = array_merge($params, $user_ids);
        }

        // Append search fields
        $like_query = '%' .$this->_wpdb->esc_like($query) . '%';
        $search_fields = ['customer_id', 'payment_id', 'payment_status', 'payment_gateway'];

        foreach ($search_fields as $field) {
            $conditions[] = "$field LIKE %s";
            $params[] = $like_query;
        }

        // Combine conditions
        $query_string .= ' ' . implode(' OR ', $conditions);

        // Prepare the query
        $prepared_query = $this->_wpdb->prepare($query_string, $params);

        // Debug query (for development purposes only)
        // var_dump($prepared_query);

        // Execute the query
        $results = $this->_wpdb->get_results($prepared_query);

        return $results ?: [];
    }


    public function getDistinctUser( $limit, $offset )
    {
        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                "SELECT DISTINCT user_id FROM {$this->tableName} LIMIT %d OFFSET %d",
                $limit,
                $offset
            )
        );

        if ( $results ) {
            foreach ($results as $key => $result) {
                $last_order = $this->_wpdb->get_row($this->_wpdb->prepare(
                    "SELECT * FROM {$this->tableName} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
                    $result->user_id
                ));

                if ($last_order) {
                    $results[$key]->payment_status = $last_order->payment_status;
                    $results[$key]->payment_date = $last_order->created_at;
                    $results[$key]->refund_status = $last_order->refund_status;
                    $results[$key]->refund_date = $last_order->refund_date;
                }
            }
        }

        return $results ? $results : [];
    }

    public function getLastOrderByUserId( $user_id )
    {
        $result = $this->_wpdb->get_row($this->_wpdb->prepare(
            "SELECT * FROM {$this->tableName} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
            $user_id
        ));

        return $result ? $result : [];
    }

    public function getDistinctCardCount( $date = null )
    {
        if( $date ) {
            $date = date( 'Y-m', strtotime( $date ) );
            $results = $this->_wpdb->get_results(
                $this->_wpdb->prepare(
                    "SELECT membership_plan, COUNT(DISTINCT user_id) as count FROM {$this->tableName} WHERE DATE_FORMAT(created_at, %s) = %s GROUP BY membership_plan",
                    '%Y-%m', $date
                ),
                OBJECT_K
            );
        } else {
            $results = $this->_wpdb->get_results(
                "SELECT membership_plan, COUNT(DISTINCT user_id) as count FROM {$this->tableName} GROUP BY membership_plan",
                OBJECT_K
            );
        }

        return [
            'digital' => $results['Digital Membership']->count ?? 0,
            'vip' => $results['VIP Membership']->count ?? 0,
        ];
    }

    public function getRevenue($date = null) {
        if ($date) {
            $date = date('Y-m', strtotime($date));
            $query = "
                SELECT COALESCE(SUM(amount), 0) AS total_revenue
                FROM {$this->tableName}
                WHERE DATE_FORMAT(created_at, '%Y-%m') = %s
                AND (refund_status IS NULL OR refund_status != 'refunded')
            ";

            $result = $this->_wpdb->get_row(
                $this->_wpdb->prepare($query, $date)
            );
        } else {
            $query = "
                SELECT COALESCE(SUM(amount), 0) AS total_revenue
                FROM {$this->tableName}
                WHERE refund_status IS NULL OR refund_status != 'refunded'
            ";

            $result = $this->_wpdb->get_row($query);
        }

        return $result ? $result->total_revenue : 0;
    }

    public function getOrderCountByDayForMonth( $date ) {
        $date = date('Y-m', strtotime($date));
        $query = "
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM {$this->tableName}
            WHERE DATE_FORMAT(created_at, '%Y-%m') = %s
            GROUP BY DATE(created_at)
        ";

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare($query, $date),
            OBJECT_K
        );

        return $results ? $results : [];
    }

    public function AllByDateRange( $from, $to )
    {
        $query = "SELECT * FROM {$this->tableName}  WHERE `created_at` BETWEEN %s AND %s";

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare($query, $from, $to),
            OBJECT_K
        );

        return $results ? $results : [];
    }

}