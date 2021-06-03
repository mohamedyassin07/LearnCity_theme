<?php 
/*
wpdb::get_results( string $query = null, string $output = OBJECT )
$query =  "SELECT * FROM ".$wpdb->prefix."pods_local_rate Where pandarf_parent_post_id = 1941 AND  bank_code ";
echo  $query . " </br>";
$post = $wpdb->get_row($query);
if($post == null){
    echo "it's null";
}
pre($post , 'post');
*/
?>
<?php

// Register new status
function register_awaiting_shipment_order_status() {
    register_post_status( 'wc-awaiting-shipment', array(
        'label'                     => 'Awaiting shipment',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Awaiting shipment (%s)', 'Awaiting shipment (%s)' )
    ) );
}
add_action( 'init', 'register_awaiting_shipment_order_status' );

// Add to list of WC Order statuses
function add_awaiting_shipment_to_order_statuses( $order_statuses ) {
 
    $new_order_statuses = array();
 
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
 
        $new_order_statuses[ $key ] = $status;
 
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-awaiting-shipment'] = 'Awaiting shipment';
        }
    }
 
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_awaiting_shipment_to_order_statuses' );



// Register New Order Statuses
function wpex_wc_register_post_statuses() {
	register_post_status( 'wc-custom-order-status', array(
		'label'						=> _x( 'Custom Order Status Name', 'WooCommerce Order status', 'text_domain' ),
		'public'					=> true,
		'exclude_from_search'		=> false,
		'show_in_admin_all_list'	=> true,
		'show_in_admin_status_list'	=> true,
		'label_count'				=> _n_noop( 'Approved (%s)', 'Approved (%s)', 'text_domain' )
	) );
}
add_filter( 'init', 'wpex_wc_register_post_statuses' );

// Add New Order Statuses to WooCommerce
function wpex_wc_add_order_statuses( $order_statuses ) {
	$order_statuses['wc-custom-order-status'] = _x( 'Custom Order Status Name', 'WooCommerce Order status', 'text_domain' );
	return $order_statuses;
}
add_filter( 'wc_order_statuses', 'wpex_wc_add_order_statuses' );

add_filter( 'dokan_query_var_filter', 'dokan_load_document_menu' );
function dokan_load_document_menu( $query_vars ) {
    $query_vars['help'] = 'help';
    return $query_vars;
}
add_filter( 'dokan_get_dashboard_nav', 'dokan_add_help_menu' );
function dokan_add_help_menu( $urls ) {
    $urls['help'] = array(
        'title' => __( 'Help', 'dokan'),
        'icon'  => '<i class="fa fa-user"></i>',
        'url'   => dokan_get_navigation_url( 'help' ),
        'pos'   => 51
    );
    return $urls;
}
add_action( 'dokan_load_custom_template', 'dokan_load_template' );
function dokan_load_template( $query_vars ) {
    if ( isset( $query_vars['help'] ) ) {
        new_tab();
        
       }
}

function new_tab(){
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dahsboard template for Fron-end
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
<div class="dokan-dashboard-wrap">
    <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
    ?>

    <div class="dokan-dashboard-content">

        <?php
            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked show_seller_dashboard_notice
             *
             *  @since 2.4
             */
            do_action( 'dokan_help_content_inside_before' );
        ?>

        <article class="help-content-area">
        	<h1>Add Your Content</h1>
          	<p>Lorem ipsum dolor sit amet</p>

        </article><!-- .dashboard-content-area -->

         <?php
            /**
             *  dokan_dashboard_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php
        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->
<?php } 






add_action( 'woocommerce_cart_calculate_fees','ts_add_discount', 20, 1 );

function ts_add_discount( $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
  
    $label_text=__("");
    $discount_amount=0;

    // Mention the payment method e.g. cod, bacs, cheque or paypal


    $cart_total = $cart_object->subtotal_ex_tax;
    $chosen_payment_method = WC()->session->get('chosen_payment_method');  //Get the selected payment method

    if( $chosen_payment_method == "paypal" ){

              $label_text = __( "PayPal Discount" );

              // The discount amount to apply
              $discount_amount = 200; 
        
    }

    else if( $chosen_payment_method == "bacs"){
              
              $label_text = __( "Direct Bank Transfer Discount" );

              // The discount amount to apply
              $discount_amount = 150; 
  
    }

    else if( $chosen_payment_method == "cheque"){
       
             $label_text = __( "Cheque Payment Discount" );
    
             // The discount amount to apply
             $discount_amount = 0; 

    }

    else {
      
             $label_text = __( "Cash-on-Delivery Discount" );
    
             // The discount amount to apply
             $discount_amount = 0; 
    }

        
    // Adding the discount
       $cart_object->add_fee( $label_text, $discount_amount, false );
    
}

add_action( 'woocommerce_review_order_before_payment', 'ts_refresh_payment_method' );
function ts_refresh_payment_method(){
    // jQuery
    ?>
    <script type="text/javascript">
        (function($){
            $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                $('body').trigger('update_checkout');
            });
        })(jQuery);
    </script>
    <?php
}