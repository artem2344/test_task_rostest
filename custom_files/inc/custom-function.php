<?php


add_image_size( 'doctor_image', 261, 123, true );


add_action( 'init', 'register_doctors_post_type' );

function register_doctors_post_type() {
    $labels = array(
        'name'               => 'Список докторов',
        'singular_name'      => 'Доктор',
        'add_new'            => 'Добавить доктора',
        'add_new_item'       => 'Добавить нового доктора',
        'edit_item'          => 'Реадктировать',
        'new_item'           => 'Новый доктор',
        'view_item'          => 'Посмотреть страницу доктора',
        'search_items'       => 'Искать доктора',
        'not_found'          => 'Докторов не найдено',
        'menu_name'          => 'Доктора',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'doctors' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type( 'doctors', $args );
}


function register_custom_taxonomies() {

    register_taxonomy( 'specialization', array( 'doctors' ), array(
        'hierarchical'      => true,
        'labels'            => array( 'name' => 'Специализации', 'singular_name' => 'Специализация' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    register_taxonomy( 'city', array( 'doctors' ), array(
        'hierarchical'      => false,
        'labels'            => array( 'name' => 'Города', 'singular_name' => 'Город' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );
}
add_action( 'init', 'register_custom_taxonomies' );



function display_doctor_info($field_name, $label, $suffix = '') {
    $val = get_field($field_name);
    if (empty($val)) return;

    // Если передан массив для plural_form, вызываем её
    if (is_array($suffix)) {
        $suffix = ' ' . plural_form($val, $suffix);
    }

    echo "<p>{$label}: {$val}{$suffix}</p>";
}


function get_the_term_names( $post_id, $taxonomy, $separator = ', ' ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return '';
    }
    return implode( $separator, wp_list_pluck( $terms, 'name' ) );
}



function plural_form($number, $titles) {
    $cases = [2, 0, 1, 1, 1, 2];
    return $titles[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ];
}


add_action('pre_get_posts', 'alarioshin_filter_doctors_query');

function alarioshin_filter_doctors_query($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('doctors')) {
        return;
    }

    $current_specs = isset($_GET['specialization']) ? array_filter(explode(',', $_GET['specialization'])) : [];
    $current_cities = isset($_GET['city']) ? array_filter(explode(',', $_GET['city'])) : [];

    $tax_query = ['relation' => 'AND'];

    $query->set('posts_per_page', 9);

    if (!empty($current_specs)) {
        $tax_query[] = [
            'taxonomy'         => 'specialization',
            'field'            => 'name',
            'terms'            => $current_specs,
            'include_children' => false,
            'operator'         => 'IN',
        ];
    }

    if (!empty($current_cities)) {
        $tax_query[] = [
            'taxonomy'         => 'city',
            'field'            => 'name',
            'terms'            => $current_cities,
            'include_children' => false,
            'operator'         => 'IN',
        ];
    }


    $orderby = $_GET['orderby'] ?? 'date';

    switch ($orderby) {
        case 'rating':
            $query->set('meta_key', 'rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
            break;

        case 'price':
            $query->set('meta_key', 'price');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            break;

        case 'experience':
            $query->set('meta_key', 'experience');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
            break;

        default:
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
            break;
    }

    if (count($tax_query) > 1) {
        $query->set('tax_query', $tax_query);
    }
}



function get_active_terms_from_url($target_taxonomy, $other_taxonomy) {



    $selected_other = isset($_GET[$other_taxonomy]) ? explode(',', $_GET[$other_taxonomy]) : [];



    $args = [
        'post_type'      => 'doctors',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];

    if (!empty($selected_other)) {
        $args['tax_query'] = [[
            'taxonomy' => $other_taxonomy,
            'field'    => 'name',
            'terms'    => $selected_other,
            'operator' => 'IN',
        ]];
    }


    $post_ids = get_posts($args);

    if (empty($post_ids)) return [];



    return wp_get_object_terms($post_ids, $target_taxonomy);
}


function alarioshin_enqueue_filters() {

    wp_enqueue_style( 'alarioshin_test_rostest-style', get_stylesheet_uri(), array(), _S_VERSION );

    wp_enqueue_script(
        'alarioshin-filter',
        get_template_directory_uri() . '/js/script.js',
        array('jquery'),
        _S_VERSION,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

}


add_action( 'wp_enqueue_scripts', 'alarioshin_enqueue_filters' );