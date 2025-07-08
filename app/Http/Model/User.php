<?php

namespace AK\SMARTAUTH\Http\Model;

defined('ABSPATH') || exit; // Prevent direct access

class User extends Model
{
    protected $tableName = 'users';

    public function getAll( $date =  null)
    {
        if( $date ){
            $date = date( 'Y-m', strtotime( $date ) );
            $query = "SELECT u.ID, u.user_login, u.user_email
                    FROM {$this->tableName} u
                    INNER JOIN {$this->_wpdb->usermeta} um ON u.ID = um.user_id
                    WHERE um.meta_key = %s AND um.meta_value LIKE %s AND DATE_FORMAT(u.user_registered, %s) = %s";

            $prepared_query = $this->_wpdb->prepare($query, $this->_wpdb->prefix . 'capabilities', '%subscriber%', '%Y-%m', $date);

            $results = $this->_wpdb->get_results( $prepared_query );
        } else {
            $results = $this->_wpdb->get_results("SELECT u.ID, u.user_login, u.user_email FROM {$this->tableName} u
                INNER JOIN {$this->_wpdb->usermeta} um ON u.ID = um.user_id
                WHERE um.meta_key = '{$this->_wpdb->prefix}capabilities'AND um.meta_value LIKE '%subscriber%'"
            );
        }

        return $results ? $results : [];
    }

    public function getAllByLimit( $limit, $offset ) {
        $query = "SELECT u.ID, u.user_login, u.user_email
                FROM {$this->tableName} u
                INNER JOIN {$this->_wpdb->usermeta} um ON u.ID = um.user_id
                WHERE um.meta_key = %s AND um.meta_value LIKE %s
                LIMIT %d OFFSET %d";

        $prepared_query = $this->_wpdb->prepare($query, $this->_wpdb->prefix . 'capabilities', '%subscriber%', $limit, $offset);

        return $this->_wpdb->get_results($prepared_query);
    }


    public function search($limit, $offset, $query )
    {
        // Base query string
        $query_string = "SELECT * FROM {$this->tableName} WHERE";
        // Append search fields
        $like_query = '%' .$this->_wpdb->esc_like($query) . '%';
        $query_string .= " user_login LIKE %s OR user_email LIKE %s";
        $query_string .= " LIMIT %d OFFSET %d";
        // Prepare query
        $prepared_query = $this->_wpdb->prepare( $query_string, $like_query, $like_query, $limit, $offset );

        // Execute the query
        $results = $this->_wpdb->get_results($prepared_query);

        return $results ? $results : [];
    }

    public function getAllByMonth( $date )
    {
        $date = date( 'Y-m', strtotime( $date ) );
        $query = "SELECT u.ID, u.user_login, u.user_email
                FROM {$this->tableName} u
                INNER JOIN {$this->_wpdb->usermeta} um ON u.ID = um.user_id
                WHERE um.meta_key = %s AND um.meta_value LIKE %s AND DATE_FORMAT(u.user_registered, %s) = %s";

        $prepared_query = $this->_wpdb->prepare($query, $this->_wpdb->prefix . 'capabilities', '%subscriber%', '%Y-%m', $date);

        return $this->_wpdb->get_results( $prepared_query );
    }
}