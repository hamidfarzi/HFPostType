<?php

/**
 * Class Name:     HFPostType
 * Description:    Easy Custom Post Type and Custom Taxonomy generator for WordPress
 * Author:         Hamid Farzi
 * Author URI:     https://hamidfarzi.com/
 * Version:        1.0.0
 *
 *
 * @package        hfaddons
 */

namespace HFAddons;

class HFPostType
{
    private $post_args;
    private $post_labels;
    private $post_slug;
    private $post_name;
    private $taxonomies;

    public function __construct($name, $slug, $args = [])
    {
        // Post name and slug initializing
        $this->set_post_labels($name);
        $this->post_slug = $slug;


        //Initializing Post type args instead defaults
        /* Available args:
            'public'             
            'publicly_queryable' 
            'show_ui'            
            'show_in_menu'      
            'query_var'          
            'rewrite'            
            'capability_type'    
            'has_archive'        
            'hierarchical'       
            'menu_position'      
            'menu_icon'          
            'supports'           
            'taxonomies'         
        */
        $this->set_post_args($args);



        add_action('init', array($this, 'register'), 100);
    }

    /*
    *   Register Action Callback
    *   Return: void
    */
    public function register()
    {
        //Register Wordpress Custom Post Type
        register_post_type($this->post_slug, $this->get_post_args());

        //Register Wordpress Custom Taxonomies
        if (is_array($this->taxonomies) || is_object($this->taxonomies)) {
            foreach ($this->taxonomies as $key => $taxonomy) {

                register_taxonomy($key, $this->post_slug, $taxonomy);

                add_action($key . '_edit_form_fields', array($this, 'add_custom_taxonomy_upload_field'), 10, 1);

                add_action('edited_terms', array($this, 'save_custom_taxonomy_fields'), 10, 2);

                add_action('restrict_manage_posts', array($this, 'admin_taxonomy_filter'), 10);

                if (isset($taxonomy['extra_fields'])) {
                    add_action($key . '_edit_form_fields', array($this, 'add_custom_taxonomy_extra_fields'), 10, 2);
                }

                add_filter('manage_edit-' . $key . '_columns', array($this, 'extra_columns'), 10);
                add_filter('manage_' . $key . '_custom_column', array($this, 'extra_columns_value'), 10, 3);
            }
        }
    }
    private function set_post_labels($name)
    {
        $labels = [
            'name'               => __($name, 'hfcv'),
            'singular_name'      => __($name, 'hfcv'),
            'menu_name'          => __($name . 's', 'hfcv'),
            'name_admin_bar'     => __($name, 'hfcv'),
            'add_new'            => __('Add New', 'hfcv'),
            'add_new_item'       => __('Add New ' . $name, 'hfcv'),
            'new_item'           => __('New ' . $name, 'hfcv'),
            'edit_item'          => __('Edit ' . $name, 'hfcv'),
            'view_item'          => __('View ' . $name, 'hfcv'),
            'all_items'          => __('All ' . $name . 's', 'hfcv'),
            'search_items'       => __('Search ' . $name . 's', 'hfcv'),
            'parent_item_colon'  => __('Parent ' . $name . 's :', 'hfcv'),
            'not_found'          => __('No ' . $name . 's found.', 'hfcv'),
            'not_found_in_trash' => __('No ' . $name . 's found in Trash.', 'hfcv')
        ];
        $this->post_labels = $labels;
        return $labels;
    }

    /*
    *   Get Post Labels
    *   Return: Array
    */
    public function get_post_labels()
    {
        return $this->post_labels;
    }

    private function set_post_args($args)
    {
        $default_args = [

            'labels'             => $this->get_post_labels(),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => $this->post_slug),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail'),
            'taxonomies'         => array(),
        ];

        $new_args = array_replace($default_args, $args);

        $this->post_args = $new_args;
        return $new_args;
    }

    /*
    *   Get Post Args
    *   Return: Array
    */
    public function get_post_args()
    {
        return $this->post_args;
    }

    /*
    *   Set new taxonomy for custom posttype
    *   Available args:
            'hierarchical'      
            'labels'           
            'show_ui'          
            'show_admin_column' 
            'query_var'         
            'rewrite'           
            'has_level'         
    *   Return: Array
    */
    public function set_post_tax($name, $slug, $args = [])
    {
        $labels = [
            'name'                       => __($name . 's', 'hfcv'),
            'singular_name'              => __($name, 'hfcv'),
            'search_items'               => __('Search ' . $name . 's', 'hfcv'),
            'popular_items'              => __('Popular ' . $name . 's', 'hfcv'),
            'all_items'                  => __('All ' . $name . 's', 'hfcv'),
            'parent_item'                => __('Parent ' . $name, 'hfcv'),
            'parent_item_colon'          => __('Parent ' . $name . ':', 'hfcv'),
            'edit_item'                  => __('Edit ' . $name, 'hfcv'),
            'update_item'                => __('Update ' . $name, 'hfcv'),
            'add_new_item'               => __('Add New ' . $name, 'hfcv'),
            'new_item_name'              => __('New ' . $name . ' Name', 'hfcv'),
            'separate_items_with_commas' => __('Separate ' . $name . 's with commas', 'hfcv'),
            'add_or_remove_items'        => __('Add or remove ' . $name . 's', 'hfcv'),
            'choose_from_most_used'      => __('Choose from the most used ' . $name . 's', 'hfcv'),
            'not_found'                  => __('No ' . $name . 's found.', 'hfcv'),
            'menu_name'                  => __($name . 's', 'hfcv'),
        ];

        $default_args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => $slug),
            'has_level'         => false
        ];
        $new_args = array_replace($default_args, $args);
        $this->taxonomies[$slug] = $new_args;

        return $new_args;
    }

    // Add the upload field to a specific custom taxonomy
    public function add_custom_taxonomy_upload_field($term)
    {
        echo '<div class="form-field term-group">';
        echo '<label for="taxonomy-image-button">' . __('Image', 'hfcv') . '</label>';
        echo '<input type="hidden" id="taxonomy-image" name="taxonomy_image" class="custom-media-url" value="' . get_term_meta($term->term_id, 'taxonomy_image', true) . '">';
        echo '<div id="taxonomy-image-container"><img style="max-width:100px;" src="' . get_term_meta($term->term_id, 'taxonomy_image', true) . '" ></div>';
        echo '<p><button id="taxonomy-image-button" class="button">' . __('Upload Image', 'hfcv') . '</button></p>';
        echo '</div>';
    }

    // Add the extra fields to term
    public function add_custom_taxonomy_extra_fields($term, $tax)
    {
        $html = "";
        foreach ($this->taxonomies[$tax]['extra_fields'] as $fieldKey => $fieldData) {
            if ($fieldData['type'] == 'text') {
                $value = get_term_meta($term->term_id, $fieldData['name'], true);
                if (isset($fieldData['default'])) {
                    $value = !empty($value) ? $value : $fieldData['default'];
                }

                $html = <<<EOT
                <div class="form-field term-group">
                <label for="{$fieldData['name']}">{$fieldData['label']}</label>
                <input type="text" id="{$fieldData['name']}" name="{$fieldData['name']}" class="" value="{$value}">
                </div>
                </br>
EOT;
            }
            if ($fieldData['type'] == 'select') {
                $value = get_term_meta($term->term_id, $fieldData['name'], true);
                $options = "";
                foreach ($fieldData['options'] as $key => $option) { {
                        $options .= "<option value='{$option}' " . selected($option, $value, false) . ">{$key}</option>";
                    }

                    $html = <<<EOT
                <div class="form-field term-group">
                <label for="{$fieldData['name']}">{$fieldData['label']}</label>
                <select id="{$fieldData['name']}" name="{$fieldData['name']}" class="" value="{$value}">
                {$options}
                </select>
                </div>
EOT;
                }
            }
            echo $html;
        }
    }


    // Save uploaded image when a term is added or edited
    public function save_custom_taxonomy_fields($term_id, $tax)
    {
        if (isset($_POST['taxonomy_image'])) {

            update_term_meta($term_id, 'taxonomy_image', esc_url_raw($_POST['taxonomy_image']));
        }
        if (isset($this->taxonomies[$tax])) {
            foreach ($this->taxonomies[$tax]['extra_fields'] as $fieldData) {
                if (isset($_POST[$fieldData['name']])) {
                    update_term_meta($term_id, $fieldData['name'], $_POST[$fieldData['name']]);
                }
            }
        }
    }

    // Display the taxonomy dropdown filter in the admin
    public function admin_taxonomy_filter()
    {

        if (!is_admin()) return;

        global $typenow;

        $custom_post_type = $this->post_slug;

        if ($typenow == $custom_post_type) {
            $taxonomies = get_object_taxonomies($custom_post_type, 'objects');

            foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
                $taxonomy_name = $taxonomy->label;

                // Display the taxonomy dropdown filter
                wp_dropdown_categories(array(
                    'show_option_all' => "Show All $taxonomy_name",
                    'taxonomy' => $taxonomy_slug,
                    'name' => $taxonomy_slug,
                    'orderby' => 'name',
                    'selected' => isset($_GET[$taxonomy_slug]) ? $_GET[$taxonomy_slug] : '',
                    'show_count' => true,
                    'hide_empty' => false,
                ));
            }
        }
    }

    // Taxonomy Admin Extra Culomns Title
    public function extra_columns($columns)
    {
        foreach ($this->taxonomies[$_GET['taxonomy']]['extra_columns'] as $key => $label) {
            $columns[$key] = $label;
        }
        return $columns;
    }
    // Taxonomy Admin Extra Culomns Value
    public function extra_columns_value($content, $column_name, $term_id)
    {
        if (isset($this->taxonomies[$_GET['taxonomy']]['extra_columns'][$column_name])) {
            $content = get_term_meta($term_id, $column_name, true);
        }
        return $content;
    }
}
