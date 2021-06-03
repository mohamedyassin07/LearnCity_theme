<?php 
//$parent_order_id =  156 ; 
//  pre(orders_to_be_saved($parent_order_id),"orders_to_be_saved $parent_order_id");
//sWarning: session_start(): open(C:\xampp\tmp\sess_hm2niue596gjrgaj7mvtl7mpbu, O_RDWR) failed: Permission denied (13) in C:\xampp\htdocs\msd\wp-content\plugins\tajerzone\include\shipping_methods\smsa_customised_official\Helper\session.php on line 8
//$order_id =  4348;
// do_action('woocommerce_thankyou_order_id' , $order_id);
$order_id =  4365 ;
// echo "order $order_id vendor is " . dokan_get_seller_id_by_order($order_id);

$order_id =  4371;
//echo "</br>order $order_id vendor is " . dokan_get_seller_id_by_order($order_id);

$orders =  array(
    '4365',
    '4371',
);

foreach ($orders as $order_id) {
    $seller_id = dokan_get_seller_id_by_order( $order_id );
    pre($seller_id ,  'seller id');


    $seller = new WP_User($seller_id);
    $seller_adress =  $seller->dokan_profile_settings['address'];

    // get order details data...
    $order = new WC_Order($order_id);
    $shipping_methods = $order->get_shipping_method();
        $payment_method = $order->get_payment_method();
    
    if($shipping_methods == 'SMSA Shipping')
    {
        $order->update_meta_data('_shipping_method_awb' , '_smsa');
        $order->save();
        
        $SMSA = new SMSA();
        
        $customer_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
        $currency_code = $order->get_currency();
        $currency_symbol = get_woocommerce_currency_symbol($currency_code);
        
        $weight = 0;
        foreach($order->get_items() as $item)
        {
            $_weight = get_post_meta($item->get_data()['product_id'] , '_weight' , true);
            if($_weight)
            {
                $weight += $_weight * $item->get_quantity();
            }
        }
        
        
        $data = [
            'passKey' => 'Testing0' ,
            'refNo' => $order->get_id() ,
            'sentDate' => date('Y-m-d H:i:s') ,
            'idNo' => $order->get_id() ,
            'cName' => $customer_name ,
            'cntry' => $order->get_shipping_country() , // 'Riyadh'
            'cCity' => $order->get_shipping_city() ,
            'cZip' => $order->get_shipping_postcode() ,
            'cPOBox' => $order->get_shipping_postcode() ,
            'cMobile' => $order->get_billing_phone() ,
            'cTel1' => $order->get_billing_phone() ,
            'cTel2' => '' ,
            'cAddr1' => $order->get_shipping_address_1() ,
            'cAddr2' => $order->get_shipping_address_2() ,
            'shipType' => 'DLV' ,
            'PCs' => $order->get_item_count() ,
            'cEmail' => $order->get_billing_email() ,
            'carrValue' => '' ,
            'carrCurr' => $currency_symbol ,
            'codAmt' => $payment_method == 'cod' ? $order->get_total() : '0' ,
            'weight' => $weight ,
            'custVal' => '' ,
            'custCurr' => $currency_code ,
            'insrAmt' => '' ,
            'insrCurr' => '' ,
            'itemDesc' => '' ,
            'sName' => $seller->display_name,
            'sContact' =>$seller->display_name ,
            'sAddr1' => $seller_adress['street_1'] ,
            'sAddr2' => $seller_adress['street_2'],
            'sCity' => $seller_adress['city'],
            'sPhone' => strlen($seller->dokan_profile_settings['phone']) > 4   ?  $seller->dokan_profile_settings['phone'] : '12345' ,
            'sCntry' => $seller_adress['country'] ,
            'prefDelvDate' => '' ,
            'gpsPoints' => '' ,
        ];
        pre($data ,  'data');
        remote_pre($data);
        $log_data = [
            'order_id' => $order->get_Id() ,
            'customer_id' => $order->get_customer_id() ,
            'customer_name' => $customer_name ,
            'vendor_id' => $seller_id,
            'shipping_method' =>  'SMSA',
        ];
        
        // send data soap
        $addShipMPS = $SMSA->addShipMPS($data);
        pre($addShipMPS ,  'addShipMPS');
        remote_pre($addShipMPS);
        if($addShipMPS instanceof Exception)
        {
            // submit log
            wp_insert_post([
                'post_type' => 'tjr_log' ,
                'post_excerpt' => $addShipMPS->getMessage(),
                'meta_input' => $log_data ,
            ]);
        }
        else
        {
            $awd_status = $SMSA->getStatus($addShipMPS->addShipMPSResult);
            // set awd_number order
            if(is_numeric($addShipMPS->addShipMPSResult) && $addShipMPS->addShipMPSResult > 0 )
            {
                // get status
                $order->update_meta_data('awb_number' , $addShipMPS->addShipMPSResult);
                $order->update_meta_data('awb_status' , ($awd_status instanceof Exception) ? '' : $awd_status);
                $order->save_meta_data();
            }
            
            $log_data['awb_number'] = $addShipMPS->addShipMPSResult;
            $log_data['awb_status'] = ($awd_status instanceof Exception) ? '' : $awd_status ;

            // submit log
            wp_insert_post([
                'post_type' => 'tjr_log' ,
                'post_excerpt' => $addShipMPS->addShipMPSResult ,
                'meta_input' => $log_data ,
            ]);
        }
        remote_pre($log_data);
        pre($log_data);

    }
}