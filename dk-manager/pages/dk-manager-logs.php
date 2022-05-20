<?php  
function getPaymentTypes(){

    $curl = curl_init();
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/sales/payment/type/',
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
    echo '<pre>';
    print_r(json_decode($response,true));
    echo '</pre>';
}
//getPaymentTypes();

function checkcustomerindk(){
    
    $curl = curl_init();
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/1710790000', //1710794709
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
        echo 'customer not found';
    }else{
        echo $response;
    }
    
}
//checkcustomerindk();

function createInvoice(){
    
    $curl = curl_init();
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
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
            "Number": "1710794709",
            "Name": "Þorvaldur Hafdal",
            "Address1": "Sommer Street"
        },
        "Options" :{
        	"OriginalPrices":0
        	},
        "Date": "2018-03-11T01:55:34.8544033+00:00",
        "Currency": "ISK",
        "Exchange": 1,
        "Lines": [
            {
                "ItemCode": "00001",
                "Warehouse": "BG1",
                "Text": "Example Item",
                "Text2": "Extra text",
                "Quantity": 2,
                "Reference": "ABCD",
                "IncludingVAT": false,
                "Price": 500,
                "Discount": 0,
                "DiscountAmount": 0,
                "Dim1": ""
            }
        ],
        "Payments": [
            {
                "ID": 14,
                "Name": "Mastercard",
                "Amount": 1000
            }
        ]
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
}

//echo "<br><br>-----------------------------<br><br>";

function countCustomers(){
    //https://api.dkplus.is/swagger/ui/index#!/Customer/Customer_GetCustomerCount
    $curl = curl_init();
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/page/5000/20?include=Number%2CName%2CAddress1%2CZipCode%2CPhoneMobile%2CEmail%2CNoVat%2CModified',
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
        echo "<pre>";
        print_r(json_decode($response,true));
        echo "</pre>";
    }else{
        echo "no response";
    }
    curl_close($curl);
    
}
//countCustomers();


function print_customers(){
    $curl = curl_init();
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f";
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/customer/page/4/1',
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
    echo "<pre>";
    print_r(json_decode($response,true));
    echo "</pre>";
}
//print_customers();
    
    
function createInvoiceFromWoo(){
    $order = wc_get_order( 13 );
    $order_id  = $order->get_id(); // Get the order ID
    $order_data = $order->get_data();
    
    //Customer
    $user_id   = $order->get_user_id(); // Number
    $Name = $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];
    $Address1 = $order_data['billing']['address_1'];
    $Address2 = $order_data['billing']['address_2'].' '.$order_data['billing']['city'].' '.$order_data['billing']['state'];
    $ZipCode = $order_data['billing']['postcode'];
    $Email = $order_data['billing']['email'];
    $Phone = $order_data['billing']['phone'];
    
    $user = array("Id"=>$user_id,"Name"=>$Name, "Address1"=>$Address1, "Address2"=>$Address2, "ZipCode"=>$ZipCode, "Email"=>$Email, "Phone"=>$Phone);
    
    //check if customer is new
    $Number = get_user_meta($user_id,'dk_customer_number', true);
    if($Number){
        echo $Number;
    }else{
        echo "customer not registered with DK";
        $Number = dot_send_customer_to_dk_logs($user);
        if($Number){
            update_user_meta($user_id,'dk_customer_number',$Number);
        }
        exit;
    }
    
    //Date
    $order_date_created = $order_data['date_created'];
    ob_start();
    print $order_date_created;
    $order_date = ob_get_clean();
    //echo $order_date_created;
    
    //Lines [{ItemCode:''},{},{}]
    $Lines = array();
    foreach ($order->get_items() as $item_key => $item ):
        $product        = $item->get_product();
        //$product_sku    = $product->get_sku();
        $product_sku    = 0001;
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
    $Lines = json_encode($Lines);
    
    //Payment info [{ID, Name, Amount}]
    $Payment_ID = $order->get_id();
    $Payment_Name = $order->get_payment_method_title(); 
    $Payment_Amount = $order_data['total'];
    
    //Currency
    $order_meta = get_post_meta($order_id);
    $Currency = $order_meta["_order_currency"][0];
    

    $curl = curl_init();
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
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
        "Options" :{
        	"OriginalPrices":0
        	},
        "Date": "'.$order_date.'",
        "Currency": "ISK",
        "Exchange": 1,
        "Lines": '.$Lines.',
        "Payments": [
            {
                "ID": '.$Payment_ID.',
                "Name": "'.$Payment_Name.'",
                "Amount": '.$Payment_Amount.'
            }
        ]
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        $authorization
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
}
//createInvoiceFromWoo();
//echo "<br><br>-------------------------------------------<br><br>";
//createInvoice();
    
    
function dot_send_customer_to_dk_logs($user){
    $authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3";
    $curl = curl_init();
    $cnumber = "99887769".$user["Id"];
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
    //echo $response;
    
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if($httpcode == 200){
        return $cnumber;
    }else{
        return 0;
    }
    
 //return DK customer number   
}
    
//---------------------------------------------------------------

function countProducts(){
    $curl = curl_init();
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3"; //demo
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f"; //original
    curl_setopt_array($curl, array(
      //CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/1/10?include=ItemCode%2CDescription%2CRecordCreated%2CUnitPrice1WithTax%2CExtraDesc1',
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/10/1',
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
        echo "<pre>";
        $pp = json_decode($response,true);
        print_r($pp);
        // foreach($pp as $p){
        //     $wcp = wc_get_product_id_by_sku($p['ItemCode']);
        //     if($wcp){
        //         echo $wcp;
        //         echo "<br>";
        //         //product already exists
        //     }else{
        //         echo "product not found - creating product<br>";
        //         //product doesn't exist. create product
        //     }
        //     //print_r($wcp);
        // }
        
        
    }else{
        echo "no response";
    }
    curl_close($curl);
    
}
countProducts();
    

function fetchOrders(){
    $curl = curl_init();
    //$authorization = "Authorization: Bearer 3541031f-baf2-4737-a7e8-c66396e5a5e3"; //demo
    $authorization = "Authorization: Bearer 5c19a183-bcb3-4090-9ded-4e5214e8bd0f"; //original
    curl_setopt_array($curl, array(
      //CURLOPT_URL => 'https://api.dkplus.is/api/v1/Product/page/1/10?include=ItemCode%2CDescription%2CRecordCreated%2CUnitPrice1WithTax%2CExtraDesc1',
      CURLOPT_URL => 'https://api.dkplus.is/api/v1/sales/invoice/page/1/2', //createdAfter=2020-02-17&createdBefore=2020-02-20
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
        echo "<pre>";
        $pp = json_decode($response,true);
        print_r($pp);
    }else{
        echo "no response";
    }
    curl_close($curl);
    
}
//fetchOrders();
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>

<script type="text/javascript">
    var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>

<h2>DK Logs Manager</h2>
<p>Here you can see sync history and logs...</p>
<hr>

<div>
    <div>
        <h3>DK Logs</h3>
        <ul>
            <li>Not logged yet!</li>
        </ul>
    </div>
    <div>
        <h3>Last Sync</h3>
        <p>Date: </p>
        <p>Name: </p>
    </div>
</div>
