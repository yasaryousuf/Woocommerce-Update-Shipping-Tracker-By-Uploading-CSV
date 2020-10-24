<?php

class WcustEnqueue
{

    public static function init()
    {
        $self = new self;
        add_action('admin_enqueue_scripts', array($self, 'wpCustomAdminEnqueue'));
    }

    public function wpCustomAdminEnqueue()
    {
        wp_enqueue_style('pfm_admin_form-style', WCUST_ASSETSURL . "admin/css/style.css", '1.0.0', false);

        wp_enqueue_script('pfm_admin_form-script', WCUST_ASSETSURL . "admin/js/script.js", '1.0.0', true);
        wp_localize_script('form-script', 'frontend_form_object',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            )
        );

    }

}
