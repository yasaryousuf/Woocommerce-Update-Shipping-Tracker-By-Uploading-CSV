<?php

class WcustMenu
{

    public static function init()
    {
        $self = new self;
        add_action('admin_menu', array($self, 'profileMakerMenu'));
    }

    public function profileMakerMenu()
    {
        add_submenu_page( 
            'woocommerce', 
            'Update tracking by uploading CSV', 
            'Update Tracking', 
            'manage_options', 
            'wc-update-shipment-tracking',
            array($this, 'displayUploadCsvPage')
        );
    }

    public function displayUploadCsvPage()
    {
        ob_start();
        require_once WCUST_VIEW_PATH . 'content-upload-csv-page.php';
        $content = ob_get_contents();
        return $content;
    }


}
