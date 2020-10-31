<?php

class WcustAjaxAction
{
    public static function init()
    {
        $self = new self();
        add_action("wp_ajax_upload_woocommerce_shipping_tracking_csv", array($self, 'upload_woocommerce_shipping_tracking_csv'));

    }
	
	public function get_short_link ($link) {
			$api_key = '5f3900470da2c';
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "http://www.smsalert.co.in/api/createshorturl.json?apikey={$api_key}&url={$link}",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",  
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return $response;
	}

	public function send_sms ($mobileno, $tracking) {
		$api_key = '5f3900470da2c';
		$sender = 'GAYTRI';
		$tracking_big_url = "https://tracking.dtdc.com/ctbs-tracking/customerInterface.tr?submitName=showCITrackingDetails&cnNo={$tracking}&cType=Consignment";
		$text = "Your order has been dispatched. Here is your tracking URL: $tracking_big_url";
		$encodedText = rawurlencode($text);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.smsalert.co.in/api/push.json?apikey={$api_key}&sender={$sender}&mobileno={$mobileno}&text={$encodedText}&shortenurl=1",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",  
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}
	
    public function upload_woocommerce_shipping_tracking_csv()
    {
//         $csvMimes = array(
//             'text/x-comma-separated-values', 
//             'text/comma-separated-values', 
//             'application/octet-stream', 
//             'application/x-csv', 
//             'text/x-csv', 
//             'text/csv', 
//             'application/csv', 
//             'text/plain'
//         );

//         if(!in_array($_FILES['file']['type'],$csvMimes)){
//             wp_send_json_error(["message" => "Only CSV format allowed."]);
//             exit();
//         }
        try {
            $file = new \SplFileObject($_FILES['file']['tmp_name']);
            $file->setFlags(\SplFileObject::READ_CSV);
        } catch (\Exception $th) {
            wp_send_json_error(["message" => "Couldn't read from CSV file."]); exit();
        }
		$total_items = 0;
		$updated_items = 0;
		$existed_items = 0;
		$failed_item = 0;
        foreach ($file as $row) {

            if ( !function_exists( 'wc_st_add_tracking_number' ) ) {
                wp_send_json_error(["message" => "You need WooCommerce Shipment Tracking plugin first."]);
                exit();
            }

            if (!empty($row[0]) && !empty($row[1])) {
				$total_items++;
				$order_id = trim($row[0]);
                $order = wc_get_order($order_id);
				if(!empty($order)) {
					$st = WC_Shipment_Tracking_Actions::get_instance();
					$tracking_items = $st->get_tracking_items( $order_id );
					if(empty($tracking_items)) {
						$res = wc_st_add_tracking_number( $order_id, $row[1], 'DTDC', date('Y-m-d H:i:s') );
						$order_data = $order->get_data();
						$api_response = $this->send_sms($order_data['billing']['phone'], $row[1]);
						$updated_items++;
					} else {
						$existed_items++;
					}
				} else {
					$failed_item++;
				}
            }
        }
		$failed_item-=1;
		$total_items-=1;
        wp_send_json_success([
			"message" => "Tracking updated from CSV. Total orders: {$total_items}, Total updated: {$updated_items}, Already existed: {$existed_items} and Failed entries: {$failed_item} ",
			'total_items' => $total_items,
			'updated_items' => $updated_items,
			'existed_items' => $existed_items,
			'failed_item' => $failed_item,
		]); exit();
    }
}
