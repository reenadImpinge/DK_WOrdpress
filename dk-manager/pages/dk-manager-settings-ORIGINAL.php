<?php  

$w2d_settings = json_decode(get_option( 'w2d_settings' ),true);
$w2d_settings = is_array($w2d_settings)?$w2d_settings:array();

$d2w_settings = json_decode(get_option( 'd2w_settings' ),true);
$d2w_settings = is_array($d2w_settings)?$d2w_settings:array();

$dot_price_update_enabled = get_option('dot_price_update_enabled') == "enabled" ? 'checked':' ';
$dot_customers_sync_enabled = get_option('dot_customers_sync_enabled') == "enabled" ? 'checked':' ';
$dot_products_fetch_timely_enabled = get_option('dot_products_fetch_timely_enabled') == "enabled" ? 'checked':' ';
$dot_orders_fetch_timely_enabled = get_option('dot_orders_fetch_timely_enabled') == "enabled" ? 'checked':' ';


$dot_w2dk_product_enabled = get_option('dot_w2dk_product_enabled') == "enabled" ? 'checked':' ';
$dot_w2dk_product_status = get_option('dot_w2dk_product_status') ? get_option('dot_w2dk_product_status') : '';

$dot_customer_lookup_enabled = get_option('dot_customer_lookup_enabled') == "enabled" ? 'checked': ' ';

$dot_next_sync = get_option('dot_next_sync');
$dot_last_sync = get_option('dot_last_sync');
$dot_options = get_option('dot_dk_sync_options');
if($dot_options)
    $dot_options = json_decode($dot_options, true);

$minutely = '';
$hourly = '';
$daily = '';

if($dot_options['dot_price_update_manner'] == 'mins'){
    $minutely = 'selected';
}
if($dot_options['dot_price_update_manner'] == 'hours'){
    $hourly = 'selected';
}
if($dot_options['dot_price_update_manner'] == 'days'){
    $daily = 'selected';
}


// fetching settings for customers sync
$dot_next_cs_sync = get_option('dot_next_cs_sync');
$dot_last_cs_sync = get_option('dot_last_cs_sync');
$dot_dk_cs_options = get_option('dot_dk_cs_options');
if($dot_dk_cs_options)
    $dot_dk_cs_options = json_decode($dot_dk_cs_options, true);

$csminutely = '';
$cshourly = '';
$csdaily = '';

if($dot_dk_cs_options['dot_dk_cs_type'] == 'mins'){
    $csminutely = 'selected';
}
if($dot_dk_cs_options['dot_dk_cs_type'] == 'hours'){
    $cshourly = 'selected';
}
if($dot_dk_cs_options['dot_dk_cs_type'] == 'days'){
    $csdaily = 'selected';
}

// fetching settings for products sync
$dot_next_ps_sync = get_option('dot_next_ps_sync');
$dot_last_ps_sync = get_option('dot_last_ps_sync');
$dot_dk_ps_options = get_option('dot_dk_ps_options');
if($dot_dk_ps_options)
    $dot_dk_ps_options = json_decode($dot_dk_ps_options, true);

$psminutely = '';
$pshourly = '';
$psdaily = '';

if($dot_dk_ps_options['dot_dk_ps_type'] == 'mins'){
    $csminutely = 'selected';
}
if($dot_dk_ps_options['dot_dk_ps_type'] == 'hours'){
    $cshourly = 'selected';
}
if($dot_dk_ps_options['dot_dk_ps_type'] == 'days'){
    $csdaily = 'selected';
}

// fetching settings for orders sync
$dot_next_os_sync = get_option('dot_next_os_sync');
$dot_last_os_sync = get_option('dot_last_os_sync');
$dot_dk_os_options = get_option('dot_dk_os_options');
if($dot_dk_os_options)
    $dot_dk_os_options = json_decode($dot_dk_os_options, true);

$psminutely = '';
$pshourly = '';
$psdaily = '';

if($dot_dk_os_options['dot_dk_os_type'] == 'mins'){
    $csminutely = 'selected';
}
if($dot_dk_os_options['dot_dk_os_type'] == 'hours'){
    $cshourly = 'selected';
}
if($dot_dk_os_options['dot_dk_os_type'] == 'days'){
    $csdaily = 'selected';
}


?>

<script type="text/javascript">
    var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<div id="dot_dk_container">
<h2 >DK Manager</h2>
<p>Here you can configure sync settings...</p>
    
<p><strong>Current Time: </strong><?php echo date( 'H:i:s Y-m-d ', strtotime( current_time('mysql') ) ); ?></p>

<hr>
<h3 class="dot_head">Price Update Settings</h3>
<input type="checkbox" id="dot_price_update_enabled" <?php echo $dot_price_update_enabled; ?> value="enabled">Enable Product Price Updates from DK</input><br>
<div style="display: flex; align-items: center; gap: 8px;">Check Prices in dk every 
    <input type="number" min=5 id="dot_price_duration" value="<?php echo $dot_options['dot_price_update_duration'] ?>">
    <select id="dot_price_duration_type">
        <option value="mins" <?php  echo $minutely; ?>>Minutes</option>
        <option value="hours" <?php  echo $hourly; ?>>Hours</option>
        <option value="days" <?php  echo $daily; ?>>Days</option>
    </select>
    <button id="dot_price_duration_save">Save</button>
</div>
<br>
<p id="dot_sync_save_status" class="dot_status"></p>
<p><strong>Last Price Sync: </strong><?php echo $dot_last_sync ? $dot_last_sync: 'Unknown!' ?></p>
<p><strong>Next Price Sync: </strong><?php echo $dot_next_sync ? $dot_next_sync: 'Not Set!' ?><p>
<hr>

<h3 class="dot_head">Woo to DK Product Sync Settings</h3>
<input type="checkbox" id="dot_w2dk_product_enabled" <?php echo $dot_w2dk_product_enabled; ?> value="enabled">Create Product in DK when a new product is added in WOO</input><br>
<br>
<div style="display: flex; align-items: center; gap: 8px;">
    <button id="dot_w2dk_product_save">Save</button>
</div>
<br>
<p id="dot_w2dk_product_status" class="dot_status"><?php echo $dot_w2dk_product_status; ?></p>
<hr>

<h3 class="dot_head">Enable Customer Lookup Button on Kennitala Field</h3>
<input type="checkbox" id="dot_customer_lookup_enabled" <?php echo $dot_customer_lookup_enabled; ?> value="enabled">Enable Kennitala Lookup Button in checkout</input><br>
<br>
<div style="display: flex; align-items: center; gap: 8px;">
    <button id="dot_customer_lookup_save">Save</button>
</div>
<br>
<p id="dot_customer_lookup_status" class="dot_status"><?php echo $dot_w2dk_product_status; ?></p>
<hr>
<hr>

<div class="dot_row dot_gap_50 dot_pr-50 dot_br-1">
    <div>
        <h3 class="dot_head">Customers Sync - DK to Woo</h3>
        <p>Click the button below to start fetch customers...</p>
        <p>IF you want to fetch all customers, uncheck NoVat option. It is used to keep track of customers that are already synced.</p>
        <div>
            
            <div style="margin-bottom: 10px;">
                <input type="radio" name="dot_dk_customers_fetch" id="dot_dk_customers_fetch_all" value="fetch_all">Fetch All Customers<small class="dot-note"> ( This option will fetch all customers and only update sync field ) </small></input><br>
                <input type="radio" name="dot_dk_customers_fetch" id="dot_dk_customers_fetch_missing" checked value="fetch_missing">Fetch Customers that are Missing<small class="dot-note"> ( This option will use sync field to find and fetch un-synced customers from DK ) </small></input><br>
            </div>
            <div style="margin-bottom: 10px;">
                <input type="checkbox" id="dot_dk_customers_fetch_in_batches_enabled" value="enabled" checked disabled>Fetch Customers in Batches of </input><input type="number" min=2 max=100 value="20" id="dot_dk_customers_fetch_in_batches">customers.<br>
                <input type="checkbox" id="dot_dk_customers_fetch_batches" value="enabled" checked disabled>Total No. of Batches </input><input type="number" value="20" min=2 max="500" id="dot_dk_customers_fetch_batches_no"><br>
                <input type="checkbox" id="dot_dk_customers_fetch_tfield_enabled" value="enabled">To identify un-synced customers, Use DK Field </input><input type="text" value="NoVat" id="dot_dk_customers_fetch_tfield"> <br>
            </div>
            <button id="dot_fetch_customers_from_woo">Fetch Customers Now</button>
        </div>
        <br>
        <hr>
        <br>
        <div>
            <div style="margin-bottom: 10px;">
            <input  type="checkbox" id="dot_customers_sync_enabled" <?php echo $dot_customers_sync_enabled; ?> value="enabled">Check for new Customers in DK </input><br>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">Check New Customers in DK every 
                <input type="number" id="dot_dk_cs_duration" value="<?php echo $dot_dk_cs_options['dot_dk_cs_duration'] ?>">
                <select id="dot_dk_cs_type">
                    <option value="mins" <?php  echo $csminutely; ?>>Minutes</option>
                    <option value="hours" <?php  echo $cshourly; ?>>Hours</option>
                    <option value="days" <?php  echo $csdaily; ?>>Days</option>
                </select>
                <button id="dot_dk_cs_duration_save">Save</button>
            </div>
            <small>Set atleast 30 minutes of duration. Each run will fetch and create 20-50 new customers. To fetch all new customers, use the fetch button.</small>
            <br>
            <p id="dot_dk_cs_status" class="dot_status"></p>
            <p><strong>Last Customers Sync: </strong><?php echo $dot_last_cs_sync ? $dot_last_cs_sync: 'Unknown!' ?></p>
            <p><strong>Next Customers Sync: </strong><?php echo $dot_next_cs_sync ? $dot_next_cs_sync: 'Not Set!' ?><p>
        </div>
    </div>
    <div>
        <p id="dot_fetch_customers_from_woo_status" class="dot_status"></p>
    </div>
</div>

<hr>
<hr>

<div class="dot_row dot_gap_50 dot_pr-50 dot_br-1">
    <div>
        
        <h3 class="dot_head">Products Sync - DK to Woo</h3>
        <p>Click the button below to start fetch Products...</p>
        <p>Only those products will be fetched that does not exist in woo. </p>
        <div>
            <div style="margin-bottom: 10px;">
                <input type="radio" name="dot_dk_products_fetch" id="dot_dk_products_fetch_all" value="fetch_all">Fetch All Products<small class="dot-note"> ( This option will fetch all products and only update sync field ) </small></input><br>
                <input type="radio" name="dot_dk_products_fetch" id="dot_dk_products_fetch_missing" checked value="fetch_missing">Fetch Products that are Missing<small class="dot-note"> ( This option will use sync field to find and fetch un-synced products from DK ) </small></input><br>
            </div>
            <div style="margin-bottom: 10px;">
                <input type="checkbox" id="dot_dk_products_fetch_in_batches_enabled" value="enabled" checked disabled>Fetch Products in Batches of </input><input type="number" min=2 max=100 value="20" id="dot_dk_products_fetch_in_batches"> items.<br>
                <input type="checkbox" id="dot_dk_products_fetch_batches" value="enabled" checked disabled>Total No. of Batches </input><input type="number" value="20" min=2 max="500" id="dot_dk_products_fetch_batches_no"><br>
                <input type="checkbox" id="dot_dk_products_fetch_tfield_enabled" value="enabled">To identify un-synced products, Use DK Field </input><input type="text" value="Dim1" id="dot_dk_products_fetch_tfield"> <br>
            </div>
            <button id="dot_dk_products_fetch_btn">Fetch Products Now</button>
            
        </div>
        <br>
        <hr>
        <br>
        <div>
            <div style="margin-bottom: 10px;">
            <input  type="checkbox" id="dot_products_fetch_timely_enabled" <?php echo $dot_products_fetch_timely_enabled; ?> value="enabled">Check for new Products in DK </input><br>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">Check New Products in DK every 
                <input type="number" id="dot_dk_ps_duration" value="<?php echo $dot_dk_ps_options['dot_dk_ps_duration'] ?>">
                <select id="dot_dk_ps_type">
                    <option value="mins" <?php  echo $psminutely; ?>>Minutes</option>
                    <option value="hours" <?php  echo $pshourly; ?>>Hours</option>
                    <option value="days" <?php  echo $psdaily; ?>>Days</option>
                </select>
                <button id="dot_dk_ps_duration_save">Save</button>
            </div>
            <small>Set atleast 20 minutes of duration. Each run will fetch and create 20-40 new products. To fetch all new products, use the fetch button.</small>
            <br>
            <p id="dot_dk_ps_status" class="dot_status"></p>
            <p><strong>Last Products Sync: </strong><?php echo $dot_last_ps_sync ? $dot_last_ps_sync: 'Unknown!' ?></p>
            <p><strong>Next Products Sync: </strong><?php echo $dot_next_ps_sync ? $dot_next_ps_sync: 'Not Set!' ?><p>
        </div>
    </div>
    <div>
        <p id="dot_fetch_products_from_woo_status" class="dot_status"></p>
    </div>
</div>


<hr>
<hr>

<div class="dot_row dot_gap_50 dot_pr-50 dot_br-1">
    <div>
        
        <h3 class="dot_head">Orders Sync - DK to Woo</h3>
        <p><strong style="color:red;">(Under Development) Notice: DK Endpoint to fetch orders is missing. </strong></p>
        <p>Click the button below to start fetching Orders...</p>
        <div>
            <div style="margin-bottom: 10px;">
                <input type="radio" name="dot_dk_orders_fetch" id="dot_dk_orders_fetch_all" value="fetch_all">Fetch All Orders</input><small class="dot-note"> ( This option will fetch all orders and only update sync field ) </small><br>
                <input type="radio" name="dot_dk_orders_fetch" id="dot_dk_orders_fetch_missing" checked value="fetch_missing">Fetch Orders that are Missing</input><small class="dot-note"> ( This option will use sync field to find and fetch un-synced orders from DK ) </small><br>
            </div>
            <div style="margin-bottom: 10px;">
                <input type="checkbox" id="dot_dk_orders_fetch_in_batches_enabled" value="enabled">Fetch Orders in Batches of </input><input type="number" value="20" id="dot_dk_orders_fetch_in_batches"> Orders<br>
                <input type="checkbox" id="dot_dk_orders_fetch_tfield_enabled" value="enabled">To identify un-synced orders, Use DK Field </input><input type="text" value="NoVat" id="dot_dk_orders_fetch_tfield"> <br>
            </div>
            <button id="dot_dk_orders_fetch_btn">Fetch Orders Now</button>
            <button id="dot_dk_orders_fetch_btn_stop">Stop</button>
            
        </div>
        <br>
        <hr>
        <br>
        <div>
            <div style="margin-bottom: 10px;">
            <input  type="checkbox" id="dot_orders_fetch_timely_enabled" <?php echo $dot_orders_fetch_timely_enabled; ?> value="enabled">Check for new Orders in DK </input><br>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">Check New Orders in DK every 
                <input type="number" id="dot_dk_os_duration" value="<?php echo $dot_dk_os_options['dot_dk_os_duration'] ?>">
                <select id="dot_dk_os_type">
                    <option value="mins" <?php  echo $osminutely; ?>>Minutes</option>
                    <option value="hours" <?php  echo $oshourly; ?>>Hours</option>
                    <option value="days" <?php  echo $osdaily; ?>>Days</option>
                </select>
                <button id="dot_dk_os_duration_save">Save</button>
            </div>
            <small></small>
            <br>
            <p id="dot_dk_os_status" class="dot_status"></p>
            <p><strong>Last Orders Sync: </strong><?php echo $dot_last_os_sync ? $dot_last_os_sync: 'Unknown!' ?></p>
            <p><strong>Next Orders Sync: </strong><?php echo $dot_next_os_sync ? $dot_next_os_sync: 'Not Set!' ?><p>
        </div>
    </div>
    <div>
        <p id="dot_dk_orders_fetch_status" class="dot_status"></p>
    </div>
</div>
                
                </div>