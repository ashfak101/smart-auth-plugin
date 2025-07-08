<?php

namespace AK\SMARTAUTH\Hooks\Handler;

defined('ABSPATH') || exit; // Prevent direct access
use AK\SMARTAUTH\Foundation\AppHelper;

/**
 * FilterHooksHandler class
 * Define all action hooks methods
 */
class FilterHooksHandler
{
    use AppHelper;

    public static function make_parent_selected_on_manage_category_tag( $parent_file ) {
        global $pagenow;

        // Check if user is on the categories or tags page for 'promotion'
        if (
            $pagenow === 'edit-tags.php' &&
            isset($_GET['taxonomy']) &&
            isset($_GET['post_type']) &&
            $_GET['post_type'] === 'promotion' &&
            in_array($_GET['taxonomy'], ['category', 'post_tag'], true)
        ) {
            $parent_file = 'smart-auth';
        }

        return $parent_file;
    }

    public static function make_submenut_selected_on_manage_category_tag( $submenu_file )
    {
        global $pagenow;

        // Highlight the appropriate submenu
        if (
            $pagenow === 'edit-tags.php' &&
            isset($_GET['taxonomy']) &&
            isset($_GET['post_type']) &&
            $_GET['post_type'] === 'promotion'
        ) {
            if ($_GET['taxonomy'] === 'category') {
                $submenu_file = 'edit-tags.php?taxonomy=category&post_type=promotion';
            } elseif ($_GET['taxonomy'] === 'post_tag') {
                $submenu_file = 'edit-tags.php?taxonomy=post_tag&post_type=promotion';
            }
        }

        return $submenu_file;
    }

    public static function sa_student_discount_card_query_vars( $vars ) {
        $vars[] = 'brand_name';
        $vars[] = 'category_name';
        return $vars;
    }
}
