<?php
tjr_create_sub_order_shipping('11754');
function tjr_create_sub_order_shipping($parent_id)
{
    
    if (get_post_meta($parent_id, 'has_sub_order', true) != '1') {
        return;
    }

    $child_orders = array(
        'post_parent' => $parent_id,
        'post_type' => 'shop_order',
    );
    $child_orders = get_children($child_orders);
    $parent_order = new WC_Order($parent_id);

    $shipping_methods   = $parent_order->get_shipping_methods();
    pre($shipping_methods);
    foreach ($shipping_methods as $key => $method) {

        $method_title       = $method->get_method_title();
        $method_id          = $method->get_method_id();
        $total              = $method->get_total(); // will calculated depend on the child order data
        $tjr_method_data    = tjr_shipping_method_data($method_title);

        $calculate_tax_for = array(
            'country'  => $parent_order->get_shipping_country(),
            'state'    => $parent_order->get_shipping_state(), // (optional value)
            'postcode' => $parent_order->get_shipping_postcode(), // (optional value)
            'city'     => $parent_order->get_shipping_city(), // (optional value)
        );

        foreach ($child_orders as $key => $child_post) {
            $order_id =  $child_post->ID;
            $order =  new WC_Order($order_id);
            if ($tjr_method_data['id'] == 'smsa') {
                $smsa =  new SMSA();
                smsa_add_ship_mps($order_id);
                $rate =  100;
            }elseif ($tjr_method_data['id'] == 'aramex') {
                $rate =  200;
            }

            // // set the shipping rate for the child order
            // if($rate >  0){
            //     $item = new WC_Order_Item_Shipping();
            //     $item->set_method_title($method_title);
            //     $item->set_method_id($method_id); // set an existing Shipping method rate ID
            //     $item->set_total( $rate ); // (optional)
            //     $item->calculate_taxes($calculate_tax_for);
            //     $order->add_item( $item );
            //     $order->calculate_totals();    
            // }

            // reduce the $rate from the parent shipping rate
            // save the date for the current order
        }


        // $order->save(); // If you don't update the order status



        // get the method 
        //pre($method_data, $key);
        // get the rates
        // make a bill 
        // get the rate 
        // create the new WC_Order_Item_Shipping
        // reduce the amount of the shipping for the parent order 
    }
    // $country_code = $parent_order->get_shipping_country();

    // // Set the array for tax calculations
    // $calculate_tax_for = array(
    //     'country' => $country_code,
    //     'state' => '', // Can be set (optional)
    //     'postcode' => '', // Can be set (optional)
    //     'city' => '', // Can be set (optional)
    // );

    // // Optionally, set a total shipping amount
    // $new_ship_price = 5.10;

    // // Get a new instance of the WC_Order_Item_Shipping Object
    // $item = new WC_Order_Item_Shipping();

    // $item->set_method_title( "Flat rate" );
    // $item->set_method_id( "flat_rate:14" ); // set an existing Shipping method rate ID
    // $item->set_total( $new_ship_price ); // (optional)
    // $item->calculate_taxes($calculate_tax_for);

    // $order->add_item( $item );

    // $order->calculate_totals();

    // $order->update_status('on-hold');

    // // $order->save(); // If you don't update the order status









    // $data = array();
    // $data['order_id'] = $order_id;
    // $data['parent_order'] =  $parent_order;
    // $data['applied_shipping_method'] = $applied_shipping_method;
    // return $applied_shipping_method;
}
