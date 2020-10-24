<?php

class WcustAjaxAction
{
    public static function init()
    {
        $self = new self();
        add_action("wp_ajax_upload_woocommerce_shipping_tracking_csv", array($self, 'upload_woocommerce_shipping_tracking_csv'));

    }

    public function upload_woocommerce_shipping_tracking_csv()
    {
        $csvMimes = array(
            'text/x-comma-separated-values', 
            'text/comma-separated-values', 
            'application/octet-stream', 
            'application/x-csv', 
            'text/x-csv', 
            'text/csv', 
            'application/csv', 
            'text/plain'
        );

        if(!in_array($_FILES['file']['type'],$csvMimes)){
            wp_send_json_error(["message" => "Only CSV format allowed."]);
            exit();
        }
        try {
            $file = new \SplFileObject($_FILES['file']['tmp_name']);
            $file->setFlags(\SplFileObject::READ_CSV);
        } catch (\Exception $th) {
            wp_send_json_error(["message" => "Couldn't read from CSV file."]); exit();
        }
        foreach ($file as $row) {

            if ( !function_exists( 'wc_st_add_tracking_number' ) ) {
                wp_send_json_error(["message" => "You need WooCommerce Shipment Tracking plugin first."]);
                exit();
            }
            if (!empty($row[0]) && !empty($row[1])) {
                try {
                    wc_st_add_tracking_number( $row[0], $row[1], 'DTDC', date('Y-m-d H:i:s') );
                } catch(Exception $e) {
                    
                }
            }
        }
        wp_send_json_success(["message" => "Tracking updated from CSV."]); exit();
    }
}
