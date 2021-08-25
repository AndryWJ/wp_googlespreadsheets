<?php
include_once PLUGIN_DIR . '/includes/CMB2-develop/init.php';

class googleSheetsOptions extends googleSheets {

    static function init(){
        add_action( 'cmb2_admin_init', array('googleSheetsOptions','create_theme_options'));
        add_action( 'cmb2_render_wizard_link', array('googleSheetsOptions','cmb2_render_wizard_link'), 10, 5 );
    }

    static function create_theme_options(){
        $fields = array(
            [
                'name' => 'Налаштування API',
                'id' => 'wizard_settings',
                'type' => 'wizard_link'
            ]
        );

        new_cmb2_box( array(
            'id'           => PLUGIN_CLASS_NAME_SHEATS,
            'title'        => esc_html__( 'Налаштування плагіна', PLUGIN_CLASS_NAME_SHEATS ),
            'object_types' => array( 'options-page' ),
            'option_key'      => PLUGIN_CLASS_NAME_SHEATS,
            'parent_slug'     => 'options-general.php',
            'save_button'     => esc_html__( 'Зберегти налаштування', PLUGIN_CLASS_NAME_SHEATS ),
            'fields' => $fields,
            'capability' => 'edit_posts'
        ));
    }


    static function cmb2_render_wizard_link( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        if(self::$is_token){
            echo "API налаштовано!";
        }else{
            echo '<a href="/google_sheets?step=1" traget="_blank">Перейти до налаштування API</a>';
        }
    }
    

}

googleSheetsOptions::init();