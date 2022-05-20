

//dot_save_w2d_settings 
jQuery('#w2d_settings').on('submit', function (e) {
    
    e.preventDefault();
    console.log('saving w2d_settings');
    var dataa = jQuery('#w2d_settings').serialize();
    jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#w2d-status' ).html( response.message );
    });

});

//dot_save_d2w_settings
jQuery('#d2w_settings').on('submit', function (e) {
    
    e.preventDefault();
    console.log('d2w_settings');
    var dataa = jQuery('#d2w_settings').serialize();
    jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#d2w-status' ).html( response.message );
    });

});

jQuery('#dot_price_duration_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_sync_save_status' ).html( 'Dot is now saving Price Sync Settings.... (wait)' );
    
    var dot_price_duration = jQuery('#dot_price_duration').val();
    var dot_price_duration_type = jQuery('#dot_price_duration_type').val();
    var dot_price_update_enabled = jQuery('#dot_price_update_enabled').is(":checked") ? 'enabled':'disabled';
    console.log(dot_price_update_enabled);

    if(!dot_price_duration){
        jQuery( '#dot_sync_save_status' ).html( 'Error: dot_price_duration is empty.' );
        return;
    }else{
        var dataa = {
            dot_price_duration: dot_price_duration,
            dot_price_duration_type: dot_price_duration_type,
            dot_price_update_enabled: dot_price_update_enabled,
            action: 'dot_dk_sync_options_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_sync_save_status' ).html( response.message );
        });
    }
    
});



jQuery('#dot_w2dk_product_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_w2dk_product_status' ).html( 'Dot is now saving product sync settings.... (wait)' );
    
    var dot_w2dk_product_enabled = jQuery('#dot_w2dk_product_enabled').is(":checked") ? 'enabled':'disabled';

    
        var dataa = {
            dot_w2dk_product_enabled: dot_w2dk_product_enabled,
            action: 'dot_w2dk_product_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_w2dk_product_status' ).html( response.message );
        });
    
});

jQuery('#dot_customer_lookup_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_customer_lookup_status' ).html( 'Dot is now saving Lookup settings.... (wait)' );
    
    var dot_customer_lookup_enabled = jQuery('#dot_customer_lookup_enabled').is(":checked") ? 'enabled':'disabled';

    
        var dataa = {
            dot_customer_lookup_enabled: dot_customer_lookup_enabled,
            action: 'dot_customer_lookup_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_customer_lookup_status' ).html( response.message );
        });
    
});



jQuery('#dot_save_condition').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_condition_status' ).html( 'Dot is now saving new condition.... (wait)' );
    
    var dotww_distance = jQuery('#dotww_distance').val();
    var dotww_price = jQuery('#dotww_price').val();

    if(!dotww_distance || !dotww_price){
        jQuery( '#dot_condition_status' ).html( 'Error: Either Token Value or Token fee is empty.' );
        return;
    }else{
        var dataa = {
            dotww_distance: dotww_distance,
            dotww_price: dotww_price,
            action: 'dot_save_condition'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_condition_status' ).html( response.message );
            location.reload();
        });
    }
    
});


//handling save button for customers sync

jQuery('#dot_dk_cs_duration_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_dk_cs_status' ).html( 'Dot is now saving Customers Sync Settings.... (wait)' );
    
    var dot_dk_cs_duration = jQuery('#dot_dk_cs_duration').val();
    var dot_dk_cs_type = jQuery('#dot_dk_cs_type').val();
    var dot_customers_sync_enabled = jQuery('#dot_customers_sync_enabled').is(":checked") ? 'enabled':'disabled';
    console.log(dot_customers_sync_enabled);

    if(!dot_price_duration){
        jQuery( '#dot_dk_cs_status' ).html( 'Error: duration is empty.' );
        return;
    }else{
        var dataa = {
            dot_dk_cs_duration: dot_dk_cs_duration,
            dot_dk_cs_type: dot_dk_cs_type,
            dot_customers_sync_enabled: dot_customers_sync_enabled,
            action: 'dot_dk_cs_options_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_dk_cs_status' ).html( response.message );
        });
    }
    
});


//handling save button for products sync
jQuery('#dot_dk_ps_duration_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_dk_ps_status' ).html( 'Dot is now saving Products Sync Settings.... (wait)' );
    
    var dot_dk_ps_duration = jQuery('#dot_dk_ps_duration').val();
    var dot_dk_ps_type = jQuery('#dot_dk_ps_type').val();
    var dot_products_fetch_timely_enabled = jQuery('#dot_products_fetch_timely_enabled').is(":checked") ? 'enabled':'disabled';
    console.log(dot_products_fetch_timely_enabled);

    if(!dot_price_duration){
        jQuery( '#dot_dk_ps_status' ).html( 'Error: duration is empty.' );
        return;
    }else{
        var dataa = {
            dot_dk_ps_duration: dot_dk_ps_duration,
            dot_dk_ps_type: dot_dk_ps_type,
            dot_products_fetch_timely_enabled: dot_products_fetch_timely_enabled,
            action: 'dot_dk_ps_options_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_dk_ps_status' ).html( response.message );
        });
    }
    
});

//handling save button for orders sync
jQuery('#dot_dk_os_duration_save').on('click',function(event){
    event.preventDefault();
    jQuery( '#dot_dk_os_status' ).html( 'Dot is now saving Orders Sync Settings.... (wait)' );
    
    var dot_dk_os_duration = jQuery('#dot_dk_os_duration').val();
    var dot_dk_os_type = jQuery('#dot_dk_os_type').val();
    var dot_orders_fetch_timely_enabled = jQuery('#dot_orders_fetch_timely_enabled').is(":checked") ? 'enabled':'disabled';
    console.log(dot_orders_fetch_timely_enabled);

    if(!dot_price_duration){
        jQuery( '#dot_dk_os_status' ).html( 'Error: duration is empty.' );
        return;
    }else{
        var dataa = {
            dot_dk_os_duration: dot_dk_os_duration,
            dot_dk_os_type: dot_dk_os_type,
            dot_orders_fetch_timely_enabled: dot_orders_fetch_timely_enabled,
            action: 'dot_dk_os_options_save'
        };   
        jQuery.post( ajax_url, dataa, function(response) {  
            console.log(response);
            jQuery( '#dot_dk_os_status' ).html( response.message );
        });
    }
    
});


//Async function to fetch customers in batches
//handling fetch customers button
jQuery('#dot_fetch_customers_from_woo').on('click',function(event){
    event.preventDefault();
    if(confirm("It will now fetch all customers from DK and save into Woo. Do you want to continue?")){
        jQuery( '#dot_fetch_customers_from_woo_status' ).html( '<span style="color: white;">Dot is now fetching Customers from DK.... (wait)</span>' );
         
        var msg = asyncCustomersFetchLoop();
        console.log(msg);
        
    }
});

async function asyncCustomersFetch(data){
    var resp = await fetch(ajax_url,{
                            method: "POST",
                            credentials: 'same-origin',
                            body: data,
                        });
                    return resp.text();
}
const asyncCustomersFetchLoop = async() => {
    var statusBox = document.getElementById('dot_fetch_customers_from_woo_status');
    //var useNoVat = jQuery('#dot_customers_sync_use_novat').is(":checked") ? 'yes' : 'no';
    
    var fetchWhat = jQuery('input[name="dot_dk_customers_fetch"]:checked').val();
    var useSyncField = jQuery('#dot_dk_customers_fetch_tfield_enabled').is(":checked") ? 'yes' : 'no';
    var syncField = jQuery('#dot_dk_customers_fetch_tfield').val();
    var fetchInBatches = jQuery('#dot_dk_customers_fetch_in_batches_enabled').is(":checked") ? 'yes' : 'no';
    var noOfBatches = jQuery('#dot_dk_customers_fetch_batches_no').val();
    var batchCapacity = 20;
    
    if(fetchWhat == "fetch_all"){
        noOfBatches = 500;
    }
    
    if(fetchInBatches == 'yes'){
        batchCapacity = jQuery('#dot_dk_customers_fetch_in_batches').val();
    }
    
    
    const data = new FormData();
    data.append('action', 'dot_fetch_customers_from_dk_now');
    data.append('fetchWhat', fetchWhat);
    data.append('useSyncField', useSyncField);
    data.append('syncField', syncField);
    data.append('fetchInBatches', fetchInBatches);
    data.append('batchCapacity', batchCapacity);
    
    for(var i=1; i<noOfBatches; i++){
        data.append('batchNo', i);
        
        jQuery( '#dot_fetch_customers_from_woo_status' ).append( '<br><span style="color: white;">Dot is now fetching batch:'+i+' of '+batchCapacity+' Customers from DK.... (wait)</span><br>' );
        
        const msg = await asyncCustomersFetch(data);
        //console.log(msg);
        jQuery( '#dot_fetch_customers_from_woo_status' ).append( msg );
        statusBox.scrollTop = statusBox.scrollHeight;
        if(msg == 0){
            jQuery( '#dot_fetch_customers_from_woo_status' ).append( '<br><span style="color: white;">Finished: '+i+' batches were fetched. </span><br>' );
            break;
        }
        
    }
}

//Async function to fetch customers ends



//Async function to fetch products in batches
//handling fetch products button
jQuery('#dot_dk_products_fetch_btn').on('click',function(event){
    event.preventDefault();
    if(confirm("It will now fetch all Products from DK and save into Woo. Do you want to continue?")){
        jQuery( '#dot_fetch_products_from_woo_status' ).html( '<span style="color: white;">Dot is now fetching Products from DK.... (wait)</span><br>' );
        
        var msg = asyncProductsFetchLoop();
        console.log(msg);
        
    }
});

const asyncProductsFetchLoop = async() => {
    var statusBox = document.getElementById('dot_fetch_products_from_woo_status');
    
    var fetchWhat = jQuery('input[name="dot_dk_products_fetch"]:checked').val();
    var useSyncField = jQuery('#dot_dk_products_fetch_tfield_enabled').is(":checked") ? 'yes' : 'no';
    var syncField = jQuery('#dot_dk_products_fetch_tfield').val();
    var fetchInBatches = jQuery('#dot_dk_products_fetch_in_batches').is(":checked") ? 'yes' : 'no';
    var noOfBatches = jQuery('#dot_dk_products_fetch_batches_no').val();
    var batchCapacity = 20;
    
    if(fetchInBatches == 'yes'){
        batchCapacity = jQuery('#dot_dk_products_fetch_in_batches').val();
    } 
    console.log('huuuuuuu');
    const data = new FormData();
    data.append('action', 'dot_fetch_products_from_dk_now');
    data.append('fetchWhat', fetchWhat);
    data.append('useSyncField', useSyncField);
    data.append('syncField', syncField);
    data.append('fetchInBatches', fetchInBatches);
    data.append('batchCapacity', batchCapacity);
    
    for(var i=1; i<=noOfBatches; i++){
        data.append('batchNo', i);
        jQuery( '#dot_fetch_products_from_woo_status' ).append( '<br><span style="color: white;">Dot is now fetching batch:'+i+' of '+batchCapacity+' Products from DK.... (wait)</span><br>' );
        
        const msg = await asyncProductsFetch(data);
        //console.log(msg);
        jQuery( '#dot_fetch_products_from_woo_status' ).append( msg );
        console.log(statusBox.scrollHeight);
        statusBox.scrollTop = statusBox.scrollHeight + 200;
        if(msg == 0){
            jQuery( '#dot_fetch_products_from_woo_status' ).append( '<br><span style="color: white;">Finished: All Batches Fetched !!! Thanks.</span><br>' );
            break;
        }
        
    }
}

async function asyncProductsFetch(data){
    var resp = await fetch(ajax_url,{
                            method: "POST",
                            credentials: 'same-origin',
                            body: data,
                        });
                    return resp.text();
}


//Async function to fetch products ends




var breakloop = false;

//handling fetch producs button
// jQuery('#dot_dk_products_fetch_btn').on('click',function(event){
//     event.preventDefault();
//     jQuery('#dot_dk_products_fetch_btn_stop').show();
//     breakloop = false;
//     if(confirm("It will now fetch Products from DK and save into Woo. Do you want to continue?")){
        
//         jQuery( '#dot_fetch_products_from_woo_status' ).html( 'Dot is now fetching Products from DK.... (wait)' );
//         var fetchWhat = jQuery('input[name="dot_dk_products_fetch"]:checked').val();
//         var useSyncField = jQuery('#dot_dk_products_fetch_tfield_enabled').is(":checked") ? 'yes' : 'no';
//         var syncField = jQuery('#dot_dk_products_fetch_tfield').val();
//         var fetchInBatches = jQuery('#dot_dk_products_fetch_in_batches').is(":checked") ? 'yes' : 'no';
//         var batchCapacity = 10;
//         if(fetchInBatches == 'yes'){
//             batchCapacity = jQuery('#dot_dk_products_fetch_in_batches').val();
//         }
//         var dataa = {
//                 fetchWhat: fetchWhat,
//                 useSyncField: useSyncField,
//                 syncField: syncField,
//                 fetchInBatches: fetchInBatches,
//                 batchCapacity: batchCapacity,
//                 action: 'dot_fetch_products_from_dk_now'
//         };  
//         console.log(dataa);
        
//         var last_res = 1;
//         var total_c = 0;
//         var breakl = false;
        
//         for(var i=1; i<3; i++){
//             if(breakloop){
//                 break;
//             }
//             jQuery( '#dot_fetch_products_from_woo_status' ).append( "<br>fetching batch "+i+" of 20 products...\n" );
//             jQuery.ajax({
//                 type: 'POST',
//                 url: ajax_url,
//                 data: dataa,
//                 success: function(response){
//                     console.log(response);
//                     if(response == 0){
//                         breakl= true;
//                     }
//                     total_c += 20;
//                     jQuery( '#dot_fetch_products_from_woo_status' ).append( response );
//                 },
//                 //async: false,
                
//             });
            
            
//             // jQuery.post( ajax_url, dataa , function(response) {  
//             //     console.log(response);
//             //     if(response == 0){
//             //         breakl= true;
//             //     }
//             //     total_c += 20;
//             //     jQuery( '#dot_fetch_products_from_woo_status' ).append( response );
//             // });
//             if(breakl == true){
//                 break;
//             }
//         }
//         jQuery('#dot_dk_products_fetch_btn_stop').hide();
//     }
// });

jQuery('#dot_dk_products_fetch_btn_stop').on('click',function(event){
    event.preventDefault();
    breakloop = true;
    jQuery('#dot_dk_products_fetch_btn_stop').hide();
    
});


//on price update selector change
jQuery('select#dot_price_duration_type').on('change', function() {
    var puduration = jQuery("#dot_price_duration").val();
    if(this.value=="mins"){
       jQuery("#dot_price_duration").attr({"min" : 5 });
        if(puduration < 5){
        	jQuery("#dot_price_duration").val(5);
        }
    }else{
    	jQuery("#dot_price_duration").attr({"min" : 1 });
    }
});