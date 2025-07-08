<?php

namespace AK\SMARTAUTH\Http\Model;

defined('ABSPATH') || exit; // Prevent direct access

class SavedOffer extends Model
{
    protected $tableName = 'yo_card_saved_offers';

    /**
     * Retrieves a saved offer by the offer ID for the current user.
     *
     * @param int $post_id The ID of the offer.
     * @return object|null The saved offer object if found, null otherwise.
     */
    public static function getByPostId( $post_id )
    {
        $model = static::getInstance();
        $user_id = get_current_user_id();
        $query = $model->_wpdb->prepare(
            "SELECT * FROM $model->tableName WHERE user_id = %d AND post_id = %d",
            $user_id,
            $post_id
        );

        return $model->_wpdb->get_row($query);
    }

    public static function getSavedOffersByUserId( $user_id, $limit, $offset )
    {
        $model = static::getInstance();
        $query = $model->_wpdb->prepare(
            "SELECT * FROM $model->tableName WHERE user_id = %d ORDER BY saved_at DESC LIMIT %d OFFSET %d",
            $user_id,
            $limit,
            $offset
        );

        return $model->_wpdb->get_results($query);
    }

    public function delete($id, $user_id){
        $query = $this->_wpdb->prepare(
            "DELETE FROM $this->tableName WHERE id = %d AND user_id = %d",
            $id,
            $user_id
        );
        return $this->_wpdb->query($query);
    }
}