<?php

namespace AK\SMARTAUTH\Http\Model;

defined('ABSPATH') || exit; // Prevent direct access

class BrowsingHistory extends Model
{
    /**
     * @var string The name of the database table.
     */
    protected $tableName = 'yo_card_browsing_history';


    public function getBrowsingHistoryByUserId( $user_id, $limit = 10, $offset = 1 ) {
        // Prepare and execute the query to retrieve all browsing history records for the specified user
        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                "SELECT post_id, visited_at FROM {$this->tableName} WHERE user_id = %d LIMIT %d OFFSET %d",
                $user_id,
                $limit,
                $offset
            ),
            ARRAY_A
        );

        return $results;
    }

    public function countBrowsingHistoryByUserId( $user_id ) {
        // Prepare and execute the query to retrieve all browsing history records for the specified user
        $results = $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tableName} WHERE user_id = %d",
                $user_id
            )
        );

        return $results;
    }

    public function is_exist($user_id, $post_id) {
        // Prepare and execute the query to check if the browsing history record exists for the specified user and post
        $result = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "SELECT * FROM {$this->tableName} WHERE user_id = %d AND post_id = %d",
                $user_id,
                $post_id
            )
        );

        return $result ? $result : null;
    }
}