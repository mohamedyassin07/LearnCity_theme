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
    foreach ($shipping_methods as $key => $method) {
        $method_title       = $method->get_method_title();
        $tjr_method_data    = tjr_shipping_method_data($method_title);

        foreach ($child_orders as $key => $child_post) {
            $order_id =  $child_post->ID;
            if ($tjr_method_data['id'] == 'smsa') {
                smsa_add_ship_mps($order_id);
            }elseif ($tjr_method_data['id'] == 'aramex') {

            }
        }
    }
}
