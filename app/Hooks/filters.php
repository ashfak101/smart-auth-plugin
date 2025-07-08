<?php
    //Prevent direct access
    defined('ABSPATH') || exit;

    use AK\SMARTAUTH\Hooks\Handler\FilterHooksHandler;
    //Register all filter hooks

    //Make parent selected on manage category tag
    add_filter('parent_file', [FilterHooksHandler::class, 'make_parent_selected_on_manage_category_tag']);
    //Make submenu selected on manage category tag
    add_filter('submenu_file',  [FilterHooksHandler::class, 'make_submenut_selected_on_manage_category_tag']);

    //Add custom query vars
    add_filter('query_vars',  [FilterHooksHandler::class, 'sa_student_discount_card_query_vars']);