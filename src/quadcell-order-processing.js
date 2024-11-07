// jQuery(document).ready(function($) {
//     $('#order-processing-form').on('submit', function(e) {
//         // Prepare the data to send
//         alert("Submitted");
//         const dataToPost = new FormData(this);
//         console.log(dataToPost);
        // $.ajax({
        //     url: quadcellOrderProcessing.ajax_url,
        //     type: 'POST',
        //     // _ajax_nonce: quadcellOrderProcessing.nonce,
        //     // action: 'provisioning_data',
        //     data: dataToPost,
        //     success: function(response) {
        //         if (response.success) {
        //             console.log('Response from API:', response.data);
        //             $('#response-container').html(JSON.stringify(response.data, null, 2));
        //         } else {
        //             console.error('Error:', response.data);
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         console.error('AJAX Error:', error);
        //     }
        // });

//     });
// })

jQuery(document).ready(function($) {
    $('#query-form-submit').on('click',function(event) {
        // Prevent the default form submission
        event.preventDefault();
// var dataToPost = {action:'quadcell_api_ajax_handler',imsi:$('#orderimsi').val(),_ajex_nonce:quadcellOrderProcessing.nonce}
        // Prepare the data to send


        // dataToPost.append('action', 'provisioning_data');
        // dataToPost.append('_ajax_nonce', quadcellOrderProcessing.nonce);

        // $.post(quadcellOrderProcessing.ajax_url, dataToPost, function(response) {
        
        //     // processData: false, // Prevent jQuery from processing the data
        //     // contentType: false, // Let the browser set the content type
   
        //         if (response.success) {
        //             console.log('Response from API:', response.data);
        //             $('#response-container').html(JSON.stringify(response.data, null, 2));
        //         } else {
        //             console.error('Error:', response.data);
        //         }
          
  
        // });

        $.ajax({
            url: quadcellOrderProcessing.ajax_url, // this is the object instantiated in wp_localize_script function
            type: 'POST',
            data:{ 
              action: 'quadcell_api_ajax_handler',
              command: $('#orderendpoint').val(), // this is the function in your functions.php that will be triggered
              fields: {imsi:$('#orderimsi').val()},
              nonce:quadcellOrderProcessing.nonce
              //nonce:quadcellOrderProcessing.nonce
            },
            success: function( data ){
              //Do something with the result from server
              console.log( data );
            }, error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
            

    });
});
});