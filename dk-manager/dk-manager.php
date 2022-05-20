<?php if ( ! defined( 'ABSPATH' ) ){ exit; }else{ clearstatcache(); }
/**
 * Plugin Name:       		Dot DK Manager
 * Description:       		This plugin facilitates communication between WooCommerce and DK.
 * Version:					1.0.0
 * Requires at least: 		4.9
 * Requires PHP:      		7.2
 * Author:            		Núna ehf.
 * Author URI:        		https://nunaehf.is
 * License:           		GPL v2 or later
 * License URI:       		https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       		dk-manager
 * Domain Path:       		/languages
 * WC requires at least:	3.4
 * WC tested up to: 		5.3
 */

/**
 * If this file is called directly, abort.
 *
 * @since 1.0.0
 */
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Register a $dk_manager_settings settings page.
 */
add_action( 'admin_menu', 'dot_register_dot_st_menu_page' );
function dot_register_dot_st_menu_page() {
    add_menu_page(
        __( 'DK Manager', 'dot-recibi-app' ), //page title
        'DK Manager', //menu title
        'manage_options', //capability
        'dk-manager/pages/dk-manager-settings.php', //menu slug
        '', //callback function (render function)
        'dashicons-xing',
        40 //position
    );
    add_submenu_page(
        'dk-manager/pages/dk-manager-settings.php',
        'DK Logs', //page title
        'DK Logs', //menu title
        'manage_options', //capability,
        'dk-manager/pages/dk-manager-logs.php',//menu slug
        '' //callback function
    );
}


//adding javascript and style to dot-st menu page
add_action( 'admin_enqueue_scripts', 'dot_add_scripts_to_dot_st_menu_page' );
function dot_add_scripts_to_dot_st_menu_page()
{
    global $pagenow;
    if ($pagenow != 'admin.php') {
        return;
    }
    // loading css
    wp_register_style( 'dot-dk-css', plugins_url( 'css/dot_style.css' , __FILE__ ), false, '1.0.0' );
    wp_enqueue_style( 'dot-dk-css' );
     
    // loading js
    wp_register_script( 'dot-dk-js', plugins_url( 'js/dot_script.js' , __FILE__ ), array('jquery-core'), false, true );
    wp_enqueue_script( 'dot-dk-js' );
}

add_action( 'wp_enqueue_scripts', 'dot_add_scripts_to_site' );
function dot_add_scripts_to_site()
{
    
    // loading css
    wp_register_style( 'dot-dk-css', plugins_url( 'css/dot_style.css' , __FILE__ ), false, '1.0.0' );
    wp_enqueue_style( 'dot-dk-css' );
    
}


/**
 * Activate the plugin.
 */
function dotww_activate() { 
    // Trigger our function that registers the warehouses taxonomy for products.
    
}
register_activation_hook( __FILE__, 'dotww_activate' );




// ajax function that gets called to save W2D DK manager Settings 
add_action("wp_ajax_dot_save_w2d_settings", "dot_save_w2d_settings");

function dot_save_w2d_settings(){
	if ( isset( $_POST['w2d_hook'] ) ) {
	    $w2d_hook = json_encode($_POST['w2d_hook']);
    	$status = update_option('w2d_settings', $w2d_hook);
        $return = array( 'message' => "You Configuration is saved.", "status" => $status, "data" => $w2d_hook );
        wp_send_json( $return );
    }else{
    	$return = array( 'message' => "Error: Make sure at least 1 option is selected!!!" );
        wp_send_json( $return );
    }
}

// ajax function that gets called to save D2W DK manager Settings 
add_action("wp_ajax_dot_save_d2w_settings", "dot_save_d2w_settings");

function dot_save_d2w_settings(){
	if ( isset( $_POST['d2w_hook'] ) ) {
	    $d2w_hook = json_encode($_POST['d2w_hook']);
    	$status = update_option('d2w_settings', $d2w_hook);
        $return = array( 'message' => "You Configuration is saved." );
        wp_send_json( $return );
    }else{
    	$return = array( 'message' => "Error: Make sure at least 1 option is selected!!!" );
        wp_send_json( $return );
    }
}


// ajax function that gets called to save price sync Settings 
add_action("wp_ajax_dot_dk_sync_options_save", "dot_dk_sync_options_save");
function dot_dk_sync_options_save() {
    if ( isset( $_POST['dot_price_duration'] ) && isset( $_POST['dot_price_duration_type'] )) {

        if(isset($_POST['dot_price_update_enabled']) && $_POST['dot_price_update_enabled'] == "enabled"){
            $estatus = update_option('dot_price_update_enabled', "enabled");
        }else{
            $estatus = update_option('dot_price_update_enabled', "disabled");
            $return = array( 'message' => "DK Price Sync is now disabled!!! Thanks."  );
            return wp_send_json( $return );
        }
        
        $dot_price_update_manner  = $_POST['dot_price_duration_type'];
        $dot_price_update_duration  = $_POST['dot_price_duration'];
        $dot_dk_sync_options = array(
            "dot_price_update_manner"  => $_POST['dot_price_duration_type'],
            "dot_price_update_duration"  => $_POST['dot_price_duration']
        );
        
        
        $status = update_option('dot_dk_sync_options', json_encode($dot_dk_sync_options));
            $next_syn_in = 0;
            if($status || $estatus){
                switch($dot_price_update_manner){
                    case 'mins':
                        $next_syn_in = $dot_price_update_duration * 60;
                        break;
                    case 'hours':
                        $next_syn_in = $dot_price_update_duration * 60 * 60;
                        break;
                    case 'days':
                        $next_syn_in = $dot_price_update_duration * 24 * 60 * 60;
                        break;
                    default:
                        $next_syn_in = 'never';
                        break;
                }
                $now = current_time( 'mysql' );
                if($next_syn_in !== 'never'){
                    $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
                }
                
                update_option('dot_next_sync', $next_syn_in);
                $return = array( 'message' => "Woo_DK_sync_options are saved!!! <br> Next Sync is set to: ".$next_syn_in );
                return wp_send_json( $return );
                
            }else{
                $return = array( 'message' => "Error: Try Again Later or Contact Support!!!".json_encode($status) );
                return wp_send_json( $return );
            }
    }else{
        $return = array( 'message' => "Error: One or more options are not missing!!!" );
        wp_send_json( $return );
    }
    
}



// ajax function that gets called to save customers sync Settings 
add_action("wp_ajax_dot_dk_cs_options_save", "dot_dk_cs_options_save");
function dot_dk_cs_options_save() {
    if ( isset( $_POST['dot_dk_cs_duration'] ) && isset( $_POST['dot_dk_cs_type'] )) {

        if(isset($_POST['dot_customers_sync_enabled']) && $_POST['dot_customers_sync_enabled'] == "enabled"){
            $estatus = update_option('dot_customers_sync_enabled', "enabled");
        }else{
            $estatus = update_option('dot_customers_sync_enabled', "disabled");
            $return = array( 'message' => "DK Customers Sync is now disabled!!! Thanks."  );
            return wp_send_json( $return );
        }
        
        $dot_cs_update_manner  = $_POST['dot_dk_cs_type'];
        $dot_cs_update_duration  = $_POST['dot_dk_cs_duration'];
        $dot_dk_cs_options = array(
            "dot_dk_cs_type"  => $_POST['dot_dk_cs_type'],
            "dot_dk_cs_duration"  => $_POST['dot_dk_cs_duration']
        );
        
        
        $status = update_option('dot_dk_cs_options', json_encode($dot_dk_cs_options));
            $next_syn_in = 0;
            if($status || $estatus){
                switch($dot_cs_update_manner){
                    case 'mins':
                        $next_syn_in = $dot_cs_update_duration * 60;
                        break;
                    case 'hours':
                        $next_syn_in = $dot_cs_update_duration * 60 * 60;
                        break;
                    case 'days':
                        $next_syn_in = $dot_cs_update_duration * 24 * 60 * 60;
                        break;
                    default:
                        $next_syn_in = 'never';
                        break;
                }
                $now = current_time( 'mysql' );
                if($next_syn_in !== 'never'){
                    $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
                }
                
                
                
                
                update_option('dot_next_cs_sync', $next_syn_in);
                $return = array( 'message' => "Woo_DK_customers_sync_options are saved!!! <br> Next Sync is set to: ".$next_syn_in );
                return wp_send_json( $return );
                
            }else{
                $return = array( 'message' => "Error: Try Again Later or Contact Support!!!".json_encode($status) );
                return wp_send_json( $return );
            }
    }else{
        $return = array( 'message' => "Error: One or more options are not missing!!!" );
        wp_send_json( $return );
    }
    
}

// ajax function that gets called to save Products sync Settings 
add_action("wp_ajax_dot_dk_ps_options_save", "dot_dk_ps_options_save");
function dot_dk_ps_options_save() {
    if ( isset( $_POST['dot_dk_ps_duration'] ) && isset( $_POST['dot_dk_ps_type'] )) {

        if(isset($_POST['dot_products_fetch_timely_enabled']) && $_POST['dot_products_fetch_timely_enabled'] == "enabled"){
            $estatus = update_option('dot_products_fetch_timely_enabled', "enabled");
        }else{
            $estatus = update_option('dot_products_fetch_timely_enabled', "disabled");
            $return = array( 'message' => "DK Products Sync is now disabled!!! Thanks."  );
            return wp_send_json( $return );
        }
        
        $dot_ps_update_manner  = $_POST['dot_dk_ps_type'];
        $dot_ps_update_duration  = $_POST['dot_dk_ps_duration'];
        $dot_dk_ps_options = array(
            "dot_dk_ps_type"  => $_POST['dot_dk_ps_type'],
            "dot_dk_ps_duration"  => $_POST['dot_dk_ps_duration']
        );
        
        
        $status = update_option('dot_dk_ps_options', json_encode($dot_dk_ps_options));
            $next_syn_in = 0;
            if($status || $estatus){
                switch($dot_ps_update_manner){
                    case 'mins':
                        $next_syn_in = $dot_ps_update_duration * 60;
                        break;
                    case 'hours':
                        $next_syn_in = $dot_ps_update_duration * 60 * 60;
                        break;
                    case 'days':
                        $next_syn_in = $dot_ps_update_duration * 24 * 60 * 60;
                        break;
                    default:
                        $next_syn_in = 'never';
                        break;
                }
                $now = current_time( 'mysql' );
                if($next_syn_in !== 'never'){
                    $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
                }
                
                
                
                
                update_option('dot_next_ps_sync', $next_syn_in);
                $return = array( 'message' => "woo_dk_products_sync_options are saved!!! <br> Next Sync is set to: ".$next_syn_in );
                return wp_send_json( $return );
                
            }else{
                $return = array( 'message' => "Error: Try Again Later or Contact Support!!!".json_encode($status) );
                return wp_send_json( $return );
            }
    }else{
        $return = array( 'message' => "Error: One or more options are not missing!!!" );
        wp_send_json( $return );
    }
}

// ajax function that gets called to save Products sync Settings 
add_action("wp_ajax_dot_dk_os_options_save", "dot_dk_os_options_save");
function dot_dk_os_options_save() {
    if ( isset( $_POST['dot_dk_os_duration'] ) && isset( $_POST['dot_dk_os_type'] )) {

        if(isset($_POST['dot_orders_fetch_timely_enabled']) && $_POST['dot_orders_fetch_timely_enabled'] == "enabled"){
            $estatus = update_option('dot_orders_fetch_timely_enabled', "enabled");
        }else{
            $estatus = update_option('dot_orders_fetch_timely_enabled', "disabled");
            $return = array( 'message' => "DK Orders Sync is now disabled!!! Thanks."  );
            return wp_send_json( $return );
        }
        
        $dot_os_update_manner  = $_POST['dot_dk_os_type'];
        $dot_os_update_duration  = $_POST['dot_dk_os_duration'];
        $dot_dk_os_options = array(
            "dot_dk_os_type"  => $_POST['dot_dk_os_type'],
            "dot_dk_os_duration"  => $_POST['dot_dk_os_duration']
        );
        
        
        $status = update_option('dot_dk_os_options', json_encode($dot_dk_os_options));
            $next_syn_in = 0;
            if($status || $estatus){
                switch($dot_os_update_manner){
                    case 'mins':
                        $next_syn_in = $dot_os_update_duration * 60;
                        break;
                    case 'hours':
                        $next_syn_in = $dot_os_update_duration * 60 * 60;
                        break;
                    case 'days':
                        $next_syn_in = $dot_os_update_duration * 24 * 60 * 60;
                        break;
                    default:
                        $next_syn_in = 'never';
                        break;
                }
                $now = current_time( 'mysql' );
                if($next_syn_in !== 'never'){
                    $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
                }
                
                
                
                
                update_option('dot_next_os_sync', $next_syn_in);
                $return = array( 'message' => "Woo_DK_orders_sync_options are saved!!! <br> Next Sync is set to: ".$next_syn_in );
                return wp_send_json( $return );
                
            }else{
                $return = array( 'message' => "Error: Try Again Later or Contact Support!!!".json_encode($status) );
                return wp_send_json( $return );
            }
    }else{
        $return = array( 'message' => "Error: One or more options are not missing!!!" );
        wp_send_json( $return );
    }
}



// ajax function that gets called to handle dot_w2dk_product_save 
add_action("wp_ajax_dot_w2dk_product_save", "dot_w2dk_product_save");
function dot_w2dk_product_save() {
    if ( isset( $_POST['dot_w2dk_product_enabled'] ) ) {

        if($_POST['dot_w2dk_product_enabled'] == "enabled"){
            $estatus = update_option('dot_w2dk_product_enabled', "enabled");
        }else{
            $estatus = update_option('dot_w2dk_product_enabled', "disabled");
        }
        
        $return = array( 'message' => "Setting saved" );
        wp_send_json( $return );
    }
    
    $return = array( 'message' => "Error: Values are invalid!!!" );
    wp_send_json( $return );
    
}

// ajax function that gets called to handle dot_w2dk_product_save 
add_action("wp_ajax_dot_customer_lookup_save", "dot_customer_lookup_save");
function dot_customer_lookup_save() {
    if ( isset( $_POST['dot_customer_lookup_enabled'] ) ) {

        if($_POST['dot_customer_lookup_enabled'] == "enabled"){
            $estatus = update_option('dot_customer_lookup_enabled', "enabled");
        }else{
            $estatus = update_option('dot_customer_lookup_enabled', "disabled");
        }
        
        $return = array( 'message' => "Setting saved" );
        wp_send_json( $return );
    }
    
    $return = array( 'message' => "Error: Values are invalid!!!" );
    wp_send_json( $return );
    
}



// registering hooks based on DK Manager Settings
add_action("init", "dot_register_dk_hooks");
function dot_register_dk_hooks(){
    
    //updating prices from dk
    
    $dot_next_sync = get_option('dot_next_sync');
                if($dot_next_sync){
                    $dot_next_sync_timestamp = strtotime($dot_next_sync);
                }else{
                    //echo 'next sync not set.';
                    return 0;
                }
                $dot_options = get_option('dot_dk_sync_options');
                if($dot_options)
                    $dot_options = json_decode($dot_options, true);
                $dot_price_update_manner  = isset( $dot_options['dot_price_update_manner'] ) ? $dot_options['dot_price_update_manner']:'';
                $dot_price_update_duration  = isset( $dot_options['dot_price_update_duration'] ) ? $dot_options['dot_price_update_duration']:'';
                
                $dot_sync_prices  = true;
                
                //checking time
                $now = current_time('mysql');
                $now_timestamp = strtotime($now);
                if($now_timestamp >= $dot_next_sync_timestamp){
                    $next_syn_in = 0;
                                switch($dot_price_update_manner){
                                    case 'mins':
                                        $next_syn_in = $dot_price_update_duration * 60;
                                        break;
                                    case 'hours':
                                        $next_syn_in = $dot_price_update_duration * 60 * 60;
                                        break;
                                    case 'days':
                                        $next_syn_in = $dot_price_update_duration * 24 * 60 * 60;
                                        break;
                                    default:
                                        $next_syn_in = 'never';
                                        break;
                                }
                                $now = current_time( 'mysql' );
                                if($next_syn_in !== 'never'){
                                    $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
                                }
                                $current_sync = date( 'H:i:s Y-m-d ', strtotime( current_time('mysql') ) );

                                update_option('dot_next_sync', $next_syn_in);
                                update_option('dot_last_sync', $current_sync);
                                $dot_price_update_enabled = get_option('dot_price_update_enabled') == "enabled" ? true:false;
                                if($dot_price_update_enabled){
                                    dot_sync_product_prices();
                                }else{
                                    return;
                                }
                                
                }else{
                    return;
                }
    
}

function dot_sync_product_prices(){
    $last_sync = json_decode(get_option( 'last_sync' ),true);
    $last_sync = is_array($last_sync)?$last_sync:array();
    
    $products = array();
    $wcproducts = wc_get_products(array('status'=>'published'));
    foreach($wcproducts as $product){
        $products[$product->get_sku()] = array(
                "price" => $product->get_regular_price(),
                "id" => $product->get_id()
            ) ;
    }


    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f"; //original
    foreach($products as $sku=>$product){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/'.$sku.'?include=ItemCode%2CUnitPrice1WithTax',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            $authorization
            ),
        ));
        
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        if($response['UnitPrice1WithTax'] != $product["price"]){
            //update product price
            //echo "product: ".$product["id"]." [". $response['UnitPrice1WithTax'] ."] not matched with". $product['price'];
            //update_post_meta($product['id'], '_regular_price', $response['UnitPrice1WithTax']);
            $_product = wc_get_product( $product['id'] );
            $_product->set_regular_price($response['UnitPrice1WithTax']);
            $_product->set_price( $response['UnitPrice1WithTax'] );
            $_product->save();
        }
        curl_close($curl);
    }
}

//add_action( 'woocommerce_new_order',  'dot_create_invoice_in_dk' );
add_action( 'woocommerce_checkout_order_processed',  'dot_create_invoice_in_dk' );
function dot_create_invoice_in_dk($order_id){
    global $woocommerce;
    $order = wc_get_order( $order_id );
    $order_data = $order->get_data();
    
    //Customer
    $customer_no = get_post_meta( $order_id, 'kennitala', true ); //kennitala
    
    $user_id   = $order->get_user_id(); // Number
    update_user_meta($user_id, 'kennitala', $customer_no);
    //$Name = $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];
    
    $Name = $order_data['billing']['company'];
    $Address1 = $order_data['billing']['address_1'];
    $Address2 = $order_data['billing']['address_2'].' '.$order_data['billing']['city'].' '.$order_data['billing']['state'];
    $ZipCode = $order_data['billing']['postcode'];
    $Email = $order_data['billing']['email'];
    $Phone = $order_data['billing']['phone'];
    
    $user = array("Id"=>$user_id,"Name"=>$Name, "Address1"=>$Address1, "Address2"=>$Address2, "ZipCode"=>$ZipCode, "Email"=>$Email, "Phone"=>$Phone);
    
    //check if customer is new
    $Number = str_replace('-', '', $customer_no);
    
    //check if customer exists in dk?
    
    $customer_exists = dot_check_customer_in_dk($Number);
    
    //if customer exists
    if($customer_exists){
        update_post_meta($order_id, 'dk_customer_status', 'Already Exists');
    }else{
        dot_send_customer_to_dk($user, $Number);
        update_post_meta($order_id, 'dk_customer_status', 'Newly Registered');
    }
    
    
    //Lines [{ItemCode:''},{},{}]
    $Lines = array();
    $order = wc_get_order( $order_id );
    $order_items = $order->get_items();
    //print_r($order_items);
    
    foreach ($order_items as $item_key => $item ):
        $product        = $item->get_product();
        $product_sku    = $product->get_sku();
        //$product_sku    = 0001;
        $Text           = $item->get_name();
        $Quantity       = $item->get_quantity();
        $Price          = $item->get_total();
        
        
        $line_item      = array( 
            "ItemCode"  => $product_sku,
            "Text"      => $Text,
            "Price"     => $Price,
            "Quantity"  => $Quantity,
            
        );
        $Lines[]        = $line_item;
    endforeach;
    
    
    //print_r($Lines);
    $Lines = json_encode($Lines);
    
    
    //Date
    $order_date_created = $order_data['date_created'];
    ob_start();
    print $order_date_created;
    $order_date = ob_get_clean();
    //echo $order_date_created;
    
    
    
    
    //Payment info [{ID, Name, Amount}]
    $Payment_ID = 7640; //$order->get_id();
    $Payment_Name = "Mastercard"; //$order->get_payment_method_title(); 
    $Payment_Amount = $order_data['total'];
    
    //Currency
    $order_meta = get_post_meta($order_id);
    $Currency = $order_meta["_order_currency"][0];
    
    //Creating Order in DK
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/sales/order',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "Customer": {
            "Number": "'.$Number.'",
            "Name": "'.$Name.'",
            "Address1": "'.$Address1.'"
        },
        "CNumber": "'.$Number.'",
        "CName": "'.$Name.'",
        "CAddress1": "'.$Address1.'",
        "OrderDate": "'.$order_date.'",
        "Currency": "ISK",
        "Reference": "website",
        "SalePerson": "WEB",
        "PaymentTerm": "IB",
        "Lines": '.$Lines.',
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    // response {"Key":"OrderNumber","Value":4455.0}
    
    $dk_order_number = json_decode($response,true);
    if(isset($dk_order_number['Value'])){
        update_post_meta($order_id, 'dk_order_no', $dk_order_number['Value']);
        update_post_meta($order_id, 'dk_order_error', 'no');
    }else{
        update_post_meta($order_id, 'dk_order_error', 'yes');
        update_post_meta($order_id, 'dk_order_error_message', $dk_order_number['Value']);
    }
    //echo $dk_order_number;
    //exit;


    //Creating Invoice in DK
    $curl = curl_init();
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3"; //demo
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/sales/invoice',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "Customer": {
            "Number": "'.$Number.'",
            "Name": "'.$Name.'",
            "Address1": "'.$Address1.'"
        },
        "CNumber": "'.$Number.'",
        "CName": "'.$Name.'",
        "CAddress1": "'.$Address1.'",
        "Options" :{
        	"OriginalPrices":0
        	},
        "Date": "'.$order_date.'",
        "Currency": "ISK",
        "Exchange": 1,
        "Lines": '.$Lines.',
        
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    //echo $response;
    $respons = json_decode($response, true);
    if(isset($respons['Number'])){
        update_post_meta($order_id, 'dk_invoice_no', $respons['Number']);
    }else{
        update_post_meta($order_id, 'dk_invoice_no', $respons['Message']);
    }
    
    //exit;
}

function dot_send_customer_to_dk($user, $customer_no){
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3"; //demo
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    $curl = curl_init();
    $cnumber = $customer_no;
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
    	"Number":"'.$cnumber.'",
    	"Name":"'.$user["Name"].'",
    	"Address1":"'.$user["Address1"].'",
    	"Address2":"'.$user["Address2"].'",
    	"ZipCode":"'.$user["ZipCode"].'",
    	"Email":"'.$user["Email"].'",
    	"Phone":"'.$user["Phone"].'",
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    echo $response;
    
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if($httpcode == 200){
        return $cnumber;
    }else{
        return 0;
    }
}

//24142,8
function dot_update_price_in_dk($product_id, $sku, $price){
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    update_post_meta( $product_id, '_update_price_in_DK', 'no' );
    //echo 'function called - '.$price;
    //$sku = "0001";
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/'.$sku,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>'{
            	"Description":"Price Update from WOO",
            	"UnitPrice1WithTax":"'.$price.'",
            }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            $authorization
            ),
        ));
            
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        //print_r($response);
        //exit;
}

//price sync woo to dk
add_action( 'woocommerce_update_product', 'dot_on_product_update', 10, 1 );
function dot_on_product_update( $product_id ) {
    $product = wc_get_product( $product_id );
    $update_price_in_DK = get_post_meta($product_id, '_update_price_in_DK', true);
        if($update_price_in_DK == "yes"){
            $new_price = get_post_meta( $product_id , '_regular_price',true);
            $sku = $product->get_sku();
            dot_update_price_in_dk($product_id, $sku, $new_price);
        }
}

//adding update price in DK field on product edit page
add_action( 'woocommerce_product_options_general_product_data', 'dot_add_price_sync_field_in_product_edit' );
function dot_add_price_sync_field_in_product_edit() {
     // Define your fields here. 
     global $woocommerce, $post;
  
      echo '<div class="options_group">';
      
        woocommerce_wp_checkbox( 
            array( 
            	'id'            => '_update_price_in_DK', 
            	'wrapper_class' => 'show_if_simple', 
            	'label'         => __('Also Update Price in DK', 'woocommerce' ), 
            	'description'   => __( 'If this option is check, price will also be updated in DK but this only works if the product with same SKU exists in DK!', 'woocommerce' ) 
            	)
        );
      
      echo '</div>';
     
}

//saving update price in DK field value on product edit page
add_action( 'woocommerce_process_product_meta', 'dot_save_price_sync_field_in_product_edit' );
function dot_save_price_sync_field_in_product_edit( $_post_id ) {
    $woocommerce_checkbox = isset( $_POST['_update_price_in_DK'] ) ? 'yes' : 'no';
	update_post_meta( $_post_id, '_update_price_in_DK', $woocommerce_checkbox );
}


//adding cutomer number [Kennitala] field in checkout
add_filter('woocommerce_checkout_fields', 'dot_add_kennitala_field');
function dot_add_kennitala_field( $fields ){
    $fields['billing']['kennitala'] = array(
        'label' => __('Kennitala', 'woocommerce'),
        'placeholder' => __('123456-1234', 'woocommerce'),
        'required' => true,
        'clear' => false,
        'type' => 'text',
        'class' => array('my-css','form-row-wide'),
        'priority' => 25,
        );
    $fields['billing']['billing_company']['required']=true;
    return $fields;
}   

//saving customer numbwer [Kennitala] field after checkout
add_action('woocommerce_checkout_update_order_meta', 'dot_save_kennitala_field');
function dot_save_kennitala_field( $order_id ){
    if(!empty($_POST['kennitala'])){
        update_post_meta( $order_id, 'kennitala', sanitize_text_field( $_POST['kennitala'] ));
    }
}

//showing customer number [Kennitala] field and customer status in admin order page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'dot_show_kennitala_and_status' );
function dot_show_kennitala_and_status( $order ){
    echo '<p><strong>'.__('Kennitala').':</strong><br/>'.get_post_meta( $order->get_id(), 'kennitala', true ). '</p>';
    echo '<p><strong>'.__('Customer Status in DK').':</strong><br/>'.get_post_meta( $order->get_id(), 'dk_customer_status', true ). '</p>';
    if(get_post_meta( $order->get_id(), 'dk_order_error', true ) == "no"){
        echo '<p><strong>'.__('Order# in DK').':</strong><br/>'.get_post_meta( $order->get_id(), 'dk_order_no', true ). '</p>';
    }else{
        echo '<p><strong>'.__('Order# in DK').':</strong><br/>'.get_post_meta( $order->get_id(), 'dk_order_error_message', true ). '</p>';
    }
    
    echo '<p><strong>'.__('Invoice# in DK').':</strong><br/>'.get_post_meta( $order->get_id(), 'dk_invoice_no', true ). '</p>';
}


//function to check if customer exists in dk
function dot_check_customer_in_dk($customer_no){
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/'.$customer_no , //1710794709 -valid customer number
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    if(empty($response)){
        //echo 'customer not found';
        return false;
        
    }else{
        $response = json_decode($response, true);
        //echo 'customer found';
        //echo $response;
        return $response;
        
    }
}


//Kennitala field format and validation with javascript
add_action('wp_footer', 'dot_validate_kennitala_field');
function dot_validate_kennitala_field(){
    if(!is_checkout()) return;
    ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.60/inputmask/jquery.inputmask.js"></script>
    <script>
        var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery(function($){
            $('input#kennitala').keyup(function(){
                
                $(this).attr("value", $(this).val());
                if($(this).val().length < 10){
                    $("input#kennitala").get(0).setCustomValidity('Invalid');
                }
                if($(this).val().length > 6){
                    $("input#kennitala").inputmask({
                        mask: "999999-9999"
                    });
                }
            });
            
            
            
            <?php 
                $dot_customer_lookup_enabled = get_option('dot_customer_lookup_enabled') == "enabled" ? true: false;
                if($dot_customer_lookup_enabled){
            ?>
                $('p#kennitala_field>span>#kennitala').after($('<input type="button" id="kennitala_lookup" value="lookup">'));
                $('p#kennitala_field>span').after($('<small id="kennitala_lookup_status"></small>'));
            
            <?php } ?>
            
            $(document).on("input", "input#kennitala", function(){
                this.value = this.value.replace(/\D/g, '');
            });
            
            $('#kennitala_lookup').on('click', function(e){
                e.preventDefault();
                $('#kennitala_lookup').val("Looking Up...");
                
                var kennitala = jQuery('input#kennitala').val();
                if(kennitala.length < 10){
                    $('#kennitala_lookup_status').html('Please Enter a Valid Kennitala. Thanks.');
                    return 0;
                }
                
                console.log(kennitala);
                var dataa = {
                    
                    kennitala: kennitala,
                    action: 'dot_lookup_customer_in_DK'
                };   
                jQuery.post( ajax_url, dataa, function(response) {  
                    console.log(response);
                    $('#kennitala_lookup').val("Look Up");
                    
                    if(response.message.Name){
                        $('#kennitala_lookup_status').html('Customer Found.');
                        
                        var name = response.message.Name.split(" ");
                        $('#billing_first_name').val(name["0"]);
                        $('#billing_last_name').val(name["1"]);
                        $('#billing_company').val(response.message.name);
                        $('#billing_address_1').val(response.message.Address1);
                        $('#billing_postcode').val(response.message.ZipCode);
                        $('#billing_phone').val(response.message.Phone);
                        $('#billing_email').val(response.message.Email);
                    }else{
                        //show customer not found
                        $('#kennitala_lookup').val("Look Up");
                        $('#kennitala_lookup_status').html('Customer Not Found.');
                        console.log('customer not found!');
                    }
                    
                    
                });
                
                
            });
            
        });
        
        
    </script>
    
    <?php
}


//replacing woo orders with DK invoice numbers
add_filter('woocommerce_order_number', 'dot_show_dk_order_number', 1, 2);
function dot_show_dk_order_number( $order_id, $order){
    $dk_order_error = get_post_meta( $order_id, 'dk_order_error', true );
    if($dk_order_error && $dk_order_error == 'no'){
        $dk_order_number =  get_post_meta( $order_id, 'dk_order_no', true );
        if($dk_order_number){
            return $dk_order_number;
        }else{
            return $order_id;
        }
    }else{
        return $order_id;
    }
}



add_action('woocommerce_after_checkout_validation', 'dot_check_kennitala', 10, 2);
function dot_check_kennitala( $fields, $errors ){
    if( !preg_match( '/^\d{6}-\d{4}$/' , $fields['kennitala'] ) ){
        $errors->add('validation', 'Please enter correct kennitala.');
    }
}


//Hook that will run to create new product in DK if enabled in settings
add_action( 'save_post', 'dot_send_product_to_DK', 50, 3 );

function dot_send_product_to_DK( $post_id, $post, $update ) {
    $dot_w2dk_product_enabled = get_option('dot_w2dk_product_enabled') == "enabled" ? true:false;
    if(!$dot_w2dk_product_enabled) return;
    
    if ( $post->post_type != 'product') return; // Only products

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id; // Exit if it's an autosave

    if ( $post->post_status != 'publish' )
        return $post_id; // Exit if not 'publish' post status

    if ( ! current_user_can( 'edit_product', $post_id ) )
        return $post_id; // Exit if user is not allowed

    // Check if product message has already been sent (avoiding repetition)
    $message_sent = get_post_meta( $post_id, '_sent_to_dk', true );
    if( $message_sent == "1" )
        return $post_id; // Exit if message has already been sent
    
    //checking if this function is enabled
    $dot_w2dk_product_enabled = get_option("dot_w2dk_product_enabled");
    if( $dot_w2dk_product_enabled == "disabled")
        return $post_id;

    ## ------------ MESSAGE ------------ ##

    // Get active price or "price" (we check if sale price exits)
    $price = empty( $_POST['_sale_price'] ) ? $_POST['_regular_price'] : $_POST['_sale_price'];
    $message = '';
    $rn = "\r\n";

    // Title
    if( ! empty( $_POST['post_title'] ) )
        $name = $_POST['post_title'];

    // Short description
    if( ! empty( $_POST['excerpt'] ) )
        $description = $_POST['excerpt'];

    // Active price
    if( ! empty( $price ) )
        $price = $price;
        
    if( ! empty( $_POST['_sku'] ) )
        $sku = $_POST['_sku'];

    $curl = curl_init();
        $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "ItemCode":"'.$sku.'",
          "Description":"'.$name.'",
          "UnitPrice1WithTax":"'.$price.'",
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            $authorization
          ),
        ));
        
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        if(isset($response['ItemCode'])){
            update_post_meta( $post_id, '_sent_to_dk', '1' );
        }else{
            update_post_meta( $post_id, '_sent_to_dk', '0' );
        }
        
        curl_close($curl);
        
}


add_action('post_submitbox_misc_actions', 'dot_show_product_dk_sync_status');
function dot_show_product_dk_sync_status($post_obj) {

  global $post;
  $post_type = 'product'; // If you want a specific post type
  $value = get_post_meta($post_obj->ID, '_sent_to_dk', true); // If saving value to post_meta
    if(!empty($value)){
        if($value == '1'){
            $msg = "Product was created in DK.";
        }else{
            $msg = "Product already existed in DK.";
        }
    }else{
        $msg = "DK Status: Unknown";
    }
    
    
  if($post_type==$post->post_type) {
    echo  '<div class="misc-pub-section misc-pub-section-last">'
         .'<label>'.$msg.'</label>'
         .'</div>';
  }
}




// Add Lookup button in kennitala field
// ajax function that gets called to save W2D DK manager Settings 
add_action("wp_ajax_dot_lookup_customer_in_DK", "dot_lookup_customer_in_DK");
add_action("wp_ajax_nopriv_dot_lookup_customer_in_DK", "dot_lookup_customer_in_DK");
function dot_lookup_customer_in_DK(){
	if ( isset( $_POST['kennitala'] ) ) {
	    $kennitala = str_replace('-', '', $_POST['kennitala']);
	    $response = dot_check_customer_in_dk($kennitala);
        $return = array( 'message' => $response, "status" => $status, "data" => $w2d_hook );
        wp_send_json( $return );
    }else{
    	$return = array( 'message' => "Error: Make sure at least 1 option is selected!!!" );
        wp_send_json( $return );
    }
}



// Fetching customers from DK
add_action("wp_ajax_dot_fetch_customers_from_dk_now", "dot_fetch_customers_from_dk_now");
add_action("wp_ajax_nopriv_dot_fetch_customers_from_dk_now", "dot_fetch_customers_from_dk_now");
function dot_fetch_customers_from_dk_now(){
    
    // novat = false means fetch customers which are not synced. customers with [novat =true] are already synced
    //$dot_use_novat = $_POST['useNoVat'] == "yes" ? "&novat=false":""; 
    $fetchWhat = $_POST['fetchWhat']; 
    $useSyncField = $_POST['useSyncField']=='yes' ? true: false;
    $syncField = $_POST['syncField'] ? "novat" : false; //only fetch products that have [syncField] false (not synced)
    $fetchInBatches = $_POST['fetchInBatches'] == 'yes' ? true: false;
    $batchCapacity = $fetchInBatches ? $_POST['batchCapacity'] : 20;
    $batchNo = $_POST['batchNo'];
    
    $fetchWhat = $_POST['fetchWhat'];
    if($useSyncField and $syncField){
        $dot_use_novat = "&".$syncField."=false";
    }else{
        $dot_use_novat = "";
    }
    
    if($fetchWhat == 'fetch_all'){
        //fetch_all_customers
        $dot_use_novat = "";
    }
    
    $dot_ccount_per_page = get_option('dot_ccount_per_page'); 
    $dot_ccount_per_page = $dot_ccount_per_page ? $dot_ccount_per_page: "20";
    
    $dot_last_page = get_option('dot_last_cpage');
    $dot_last_page = $dot_last_page ? $dot_last_page : "1";
    
        
	$curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/page/'.$batchNo.'/'.$batchCapacity.'?include=Number%2CName%2CAddress1%2CAddress2%2CZipCode%2CPhoneMobile%2CEmail%2CNoVat%2CModified'.$dot_use_novat,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    if($response){
        
        $customers = json_decode($response,true);
        if(empty($customers)){
            return 0;
        }
        
        //saving last page to keep track of how many pages are synced
        update_option('dot_last_cpage', 1);
        
        foreach($customers as $c){
            $user_login = preg_replace('/[^a-zA-Z0-9]/','',$c['Name']);
            // if(username_exists($user_login)){
            //     $user_login = $user_login."012";
            // }
            
            $user_data = array (
                'user_login' => $user_login,
                'user_email' => $c['Email'],
                'display_name' => $c['Name'],
                'role' => 'customer',
            );
            
            if($c['Email']){
                $user = wc_create_new_customer($c['Email'], $user_login, wp_generate_password());
            
                //$user = wp_insert_user( $user_data );
                if(is_wp_error($user)){
                    
                    $error_code = array_key_first( $user->errors );
                    if("registration-error-invalid-email" == $error_code){
                        echo "<p>customer # ".$c['Number']." doesnot have an email.</p>";
                        continue;
                    }else if("registration-error-email-exists" == $error_code){
                        echo "<p>customer# ".$c['Number']." with same Email already exists</p>";
                        //update customer number
                        $user = get_user_by_email($c['Email']);
                        if($user){
                            update_user_meta($user->ID, 'Kennitala', $c['Number']);
                            if($useSyncField){
                                dot_update_novat_in_DK($c['Number']);
                            }
                        }
                        continue;
                    }else{
                        echo "<p>customer#".$c['Number']."( ".$user_login." ) has error: ".$error_code."</p>";
                        continue;
                    }
                    //echo $error_code;
                    //echo $user->errors[$error_code][0];
                    //print_r($result->errors);
                
                    
                }else{
                    echo "<p>Customer # ".$c['Number']." Created.</p>";
                        update_user_meta($user->ID, 'Kennitala', $c['Number']);
                        update_user_meta($user->ID, 'billing_email', $c['Email']);
                        update_user_meta($user->ID, 'billing_address_1', $c['Address1']);
                        update_user_meta($user->ID, 'billing_address_2', $c['Address2']);
                        update_user_meta($user->ID, 'postcode', $c['ZipCode']);
                        update_user_meta($user->ID, 'phone', $c['PhoneMobile']);
                        if($useSyncField){
                                dot_update_novat_in_DK($c['Number']);
                            }
                }
            }else{
                $user = wp_insert_user( $user_data );
                if(is_wp_error($user)){
                    $error_code = array_key_first( $user->errors );
                    //echo '<p>'.$error_code;
                    echo "<p>Customer ".$user_login.": ".$user->errors[$error_code][0].'</p>';
                }else{
                    echo "<p>Customer # ".$c['Number']." created without email.</p>";
                    
                    update_user_meta($user->ID, 'Kennitala', $c['Number']);
                    update_user_meta($user->ID, 'billing_email', $c['Email']);
                            update_user_meta($user->ID, 'Kennitala', $c['Number']);
                        update_user_meta($user->ID, 'billing_email', $c['Email']);
                        update_user_meta($user->ID, 'billing_address_1', $c['Address1']);
                        update_user_meta($user->ID, 'billing_address_2', $c['Address2']);
                        update_user_meta($user->ID, 'postcode', $c['ZipCode']);
                        update_user_meta($user->ID, 'phone', $c['PhoneMobile']);
                        if($useSyncField){
                                dot_update_novat_in_DK($c['Number']);
                            }
                }
            }
            
            
        }
        
    }else{
        echo 0;
    }
    curl_close($curl);
        //$return = array( 'message' => $response, "status" => $status, "data" => $w2d_hook );
        //wp_send_json( $return );
        
}

//update Novat in DK
function dot_update_novat_in_DK($customer_number){
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/'.$customer_number,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>'{
            	"NoVat":"True",
            }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            $authorization
            ),
        ));
            
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        print_r($response);
        curl_close($curl);
}


// Fetching products from DK
add_action("wp_ajax_dot_fetch_products_from_dk_now", "dot_fetch_products_from_dk_now");
add_action("wp_ajax_nopriv_dot_fetch_products_from_dk_now", "dot_fetch_products_from_dk_now");
function dot_fetch_products_from_dk_now(){
    
    // novat = false means fetch customers which are not synced. customers with [novat =true] are already synced
    //$dot_use_novat = $_POST['useNoVat'] == "yes" ? "novat=false":""; 
    
    // $dot_pcount_per_page = get_option('dot_pcount_per_page'); 
    // $dot_pcount_per_page = $dot_pcount_per_page ? $dot_pcount_per_page: "2000";
    
    // $dot_last_ppage = get_option('dot_last_ppage');
    // $dot_last_ppage = $dot_last_ppage ? $dot_last_ppage : "1";
    $fetchWhat = $_POST['fetchWhat']; 
    $useSyncField = $_POST['useSyncField']=='yes' ? true: false;
    $syncField = $useSyncField ? $_POST['syncField'] : ""; //only fetch products that have [syncField] false (not synced)
    $fetchInBatches = $_POST['fetchInBatches'] == 'yes' ? true: false;
    $batchCapacity = $fetchInBatches ? $_POST['batchCapacity'] : 10;
    $batchNo = $_POST['batchNo'];
    
    $fetchWhat = $_POST['fetchWhat'];
    if($fetchWhat == 'fetch_all'){
        //fetch_all_products
        $result = dot_fetch_all_dk_products($useSyncField, $syncField, $batchCapacity, $batchNo);
    }else{
        //fetch_missing_products
        $result = dot_fetch_missing_dk_products($useSyncField, $syncField, $batchCapacity, $batchNo);
    }
    
    
        
}


function dot_fetch_all_dk_products($useSyncField, $syncField, $batchCapacity, $batchNo){
    
    
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f"; //original
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/'.$batchNo.'/'.$batchCapacity.'?include=ItemCode%2CDescription%2CRecordCreated%2CUnitPrice1WithTax%2CExtraDesc1',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    if($response){
        
        $products = json_decode($response,true);
        if(empty($products)){
            echo "No Products were found in DK.";
            return 0;
        }
        foreach($products as $p){
            $wcp = wc_get_product_id_by_sku($p['ItemCode']);
            if($wcp){
                echo "<p>product#".$p['ItemCode']." already exists</p>";
                continue;
            }else{
                echo "product# ".$p['ItemCode']." created in Woo.<br>";
                //product doesn't exist. create product
                
                $new_p = new WC_Product_Simple();
                $new_p->set_name($p['Description']);
                $new_p->set_price($p['UnitPrice1WithTax']);
                $new_p->set_regular_price($p['UnitPrice1WithTax']);
                $new_p->set_sku($p['ItemCode']);
                
                $new_p->set_status('publish');
                $new_p->set_catalog_visibility('visible');
                $new_p->save();
                if($useSyncField){
                    dot_update_syncField_of_product_in_DK($p['ItemCode'], $syncField);
                }
                
            }
            //print_r($wcp);
        }
        //saving last page to keep track of how many pages are synced
        update_option('dot_last_ppage', 1);
    }else{
        echo "No new products were found. Thanks";
    }
    curl_close($curl);
}

function dot_fetch_missing_dk_products($useSyncField, $syncField, $batchCapacity, $batchNo){
    
    
    $useSyncFieldNow = $useSyncField ? "&".$syncField."=false" : "";
    
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f"; //original
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/'.$batchNo.'/'.$batchCapacity.'?include=ItemCode%2CDescription%2CRecordCreated%2CUnitPrice1WithTax%2CExtraDesc1'.$useSyncFieldNow,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    if($response){
        
        $products = json_decode($response,true);
        if(empty($products)){
            echo "No Products were found in DK.";
            return 0;
        }
        foreach($products as $p){
            $wcp = wc_get_product_id_by_sku($p['ItemCode']);
            if($wcp){
                echo "<p>product#".$p['ItemCode']." already exists</p>";
                continue;
            }else{
                echo "product# ".$p['ItemCode']." created in Woo.<br>";
                //product doesn't exist. create product
                
                $new_p = new WC_Product_Simple();
                $new_p->set_name($p['Description']);
                $new_p->set_price($p['UnitPrice1WithTax']);
                $new_p->set_regular_price($p['UnitPrice1WithTax']);
                $new_p->set_sku($p['ItemCode']);
                
                $new_p->set_status('publish');
                $new_p->set_catalog_visibility('visible');
                $new_p->save();
                if($useSyncField){
                    dot_update_syncField_of_product_in_DK($p['ItemCode'], $syncField);
                }
            }
            //print_r($wcp);
        }
        //saving last page to keep track of how many pages are synced
        update_option('dot_last_ppage', 1);
    }else{
        echo "No new products were found. Thanks";
    }
    curl_close($curl);
}

//update syncField of product in DK
function dot_update_syncField_of_product_in_DK($itemCode, $syncField){
    $curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/'.$itemCode,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>'{
            	"'.$syncField.'":"'.true.'",
            }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            $authorization
            ),
        ));
            
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);
}

//update sync field for product




// checking and running customer sync if it is time and enabled
add_action("init", "dot_fetch_sync_customers_from_dk");
function dot_fetch_sync_customers_from_dk(){
    
        $enabled = get_option('dot_customers_sync_enabled') == "enabled" ? true: false;
        if(!$enabled){
            return;
        }
        
        $dot_options = get_option('dot_dk_cs_options');
        if($dot_options){
            $dot_options = json_decode($dot_options, true);
        }
        $dot_cs_update_manner  = $dot_options['dot_dk_cs_type'];
        $dot_cs_update_duration  = $dot_options['dot_dk_cs_duration'];
        
        $dot_next_sync = get_option('dot_next_cs_sync');
        if($dot_next_sync){
            $dot_next_sync_timestamp = strtotime($dot_next_sync);
        }else{
            //echo 'next sync not set.';
            return 0;
        }
        
        $dot_sync_prices  = true;
                
        //checking time
        $now = current_time('mysql');
        $now_timestamp = strtotime($now);
        if($now_timestamp >= $dot_next_sync_timestamp){
            $next_syn_in = 0;
            switch($dot_cs_update_manner){
                case 'mins':
                    $next_syn_in = $dot_cs_update_duration * 60;
                    break;
                case 'hours':
                    $next_syn_in = $dot_cs_update_duration * 60 * 60;
                    break;
                case 'days':
                    $next_syn_in = $dot_cs_update_duration * 24 * 60 * 60;
                    break;
                default:
                    $next_syn_in = 'never';
                    break;
            }
            
            if($next_syn_in !== 'never'){
                $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
            }
            
            $current_sync = date( 'H:i:s Y-m-d ', strtotime( current_time('mysql') ) );

            update_option('dot_next_cs_sync', $next_syn_in);
            update_option('dot_last_cs_sync', $current_sync);
            
            
            
            if($enabled){
                dot_fetch_sync_customers_from_dk_now();
            }else{
                return;
            }
        }
}

//function to sync customers timely
function dot_fetch_sync_customers_from_dk_now(){
    // novat = false means fetch customers which are not synced. customers with [novat =true] are already synced
    $dot_use_novat = "&novat=false"; 
    
	$curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/page/1/15?include=Number%2CName%2CAddress1%2CAddress2%2CZipCode%2CPhoneMobile%2CEmail%2CNoVat%2CModified'.$dot_use_novat,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    if($response){
        
        $customers = json_decode($response,true);
        if(empty($customers)){
            return 0;
        }
        
        foreach($customers as $c){
            $user_login = $c['Name'];
            if(username_exists($user_login)){
                $user_login = $user_login."012";
            }
            
            $user_data = array (
                'user_login' => $user_login,
                'user_email' => $c['Email'],
                'display_name' => $c['Name'],
                'role' => 'customer',
            );
            
            if($c['Email']){
                $user = wc_create_new_customer($c['Email'], $c['Name'], wp_generate_password());
            
                //$user = wp_insert_user( $user_data );
                if(is_wp_error($user)){
                    
                    $error_code = array_key_first( $user->errors );
                    if("registration-error-invalid-email" == $error_code){
                        continue;
                    }else if("registration-error-email-exists" == $error_code){
                        //update customer number
                        $user = get_user_by_email($c['Email']);
                        if($user){
                            update_user_meta($user->ID, 'Kennitala', $c['Number']);
                            dot_update_novat_in_DK($c['Number']);
                        }
                        continue;
                    }else{
                        continue;
                    }
                    //echo $error_code;
                    //echo $user->errors[$error_code][0];
                    //print_r($result->errors);
                
                    
                }else{
                        update_user_meta($user->ID, 'Kennitala', $c['Number']);
                        update_user_meta($user->ID, 'billing_email', $c['Email']);
                        update_user_meta($user->ID, 'billing_address_1', $c['Address1']);
                        update_user_meta($user->ID, 'billing_address_2', $c['Address2']);
                        update_user_meta($user->ID, 'postcode', $c['ZipCode']);
                        update_user_meta($user->ID, 'phone', $c['PhoneMobile']);
                    dot_update_novat_in_DK($c['Number']);
                }
            }else{
                $user = wp_insert_user( $user_data );
                if(is_wp_error($user)){
                    $error_code = array_key_first( $user->errors );
                }else{
                    
                    update_user_meta($user->ID, 'Kennitala', $c['Number']);
                    update_user_meta($user->ID, 'billing_email', $c['Email']);
                            update_user_meta($user->ID, 'Kennitala', $c['Number']);
                        update_user_meta($user->ID, 'billing_email', $c['Email']);
                        update_user_meta($user->ID, 'billing_address_1', $c['Address1']);
                        update_user_meta($user->ID, 'billing_address_2', $c['Address2']);
                        update_user_meta($user->ID, 'postcode', $c['ZipCode']);
                        update_user_meta($user->ID, 'phone', $c['PhoneMobile']);
                    dot_update_novat_in_DK($c['Number']);
                }
            }
        }
        
    }else{
        //echo "no response";
        return 0;
    }
    curl_close($curl);
}

// checking and running customer sync if it is time and enabled
add_action("init", "dot_fetch_sync_products_from_dk");
function dot_fetch_sync_products_from_dk(){
    
        $enabled = get_option('dot_products_sync_enabled') == "enabled" ? true: false;
        if(!$enabled){ echo 'cdvndnb';
            return;
        }
        
        $dot_options = get_option('dot_dk_ps_options');
        if($dot_options){
            $dot_options = json_decode($dot_options, true);
        }
        $dot_cs_update_manner  = $dot_options['dot_dk_ps_type'];
        $dot_cs_update_duration  = $dot_options['dot_dk_ps_duration'];
        
        $dot_next_sync = get_option('dot_next_ps_sync');
        if($dot_next_sync){
            $dot_next_sync_timestamp = strtotime($dot_next_sync);
        }else{
            //echo 'next sync not set.';
            return 0;
        }
        
        $dot_sync_prices  = true;
                
        //checking time
        $now = current_time('mysql');
        $now_timestamp = strtotime($now);
        if($now_timestamp >= $dot_next_sync_timestamp){
            $next_syn_in = 0;
            switch($dot_ps_update_manner){
                case 'mins':
                    $next_syn_in = $dot_ps_update_duration * 60;
                    break;
                case 'hours':
                    $next_syn_in = $dot_ps_update_duration * 60 * 60;
                    break;
                case 'days':
                    $next_syn_in = $dot_ps_update_duration * 24 * 60 * 60;
                    break;
                default:
                    $next_syn_in = 'never';
                    break;
            }
            
            if($next_syn_in !== 'never'){
                $next_syn_in = date( 'H:i:s Y-m-d', strtotime( $now ) + $next_syn_in);
            }
            
            $current_sync = date( 'H:i:s Y-m-d ', strtotime( current_time('mysql') ) );

            update_option('dot_next_ps_sync', $next_syn_in);
            update_option('dot_last_ps_sync', $current_sync);
            
            
            
            if($enabled){
                dot_fetch_sync_products_from_dk_now();
            }else{
                return;
            }
        }
}

//function to sync products timely
function dot_fetch_sync_products_from_dk_now(){
    // Dim1 = false means fetch products which are not synced. products with [Dim1 =true] are already synced
    $useSyncFieldNow = "&Dim1=false"; 
    $syncField = "Dim1";
    
	$curl = curl_init();
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/1/10?include=ItemCode%2CDescription%2CRecordCreated%2CUnitPrice1WithTax%2CExtraDesc1'.$useSyncFieldNow,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    if($response){
        
        $products = json_decode($response,true);
        
        if(empty($products)){
            //echo "No Products were found in DK.";
            exit;
        }
        foreach($products as $p){
            $wcp = wc_get_product_id_by_sku($p['ItemCode']);
            if($wcp){
                //echo "<p>product#".$p['ItemCode']." already exists</p>";
                continue;
            }else{
                //echo "product# ".$p['ItemCode']." created in Woo.<br>";
                //product doesn't exist. create product
                
                $new_p = new WC_Product_Simple();
                $new_p->set_name($p['Description']);
                $new_p->set_price($p['UnitPrice1WithTax']);
                $new_p->set_regular_price($p['UnitPrice1WithTax']);
                $new_p->set_sku($p['ItemCode']);
                
                $new_p->set_status('publish');
                $new_p->set_catalog_visibility('visible');
                $new_p->save();
                dot_update_syncField_of_product_in_DK($p['ItemCode'], $syncField);
            }
            //print_r($wcp);
        }
        
    }else{
        //echo "no response";
        exit;
    }
    curl_close($curl);
}