jQuery(document).ready(function($) {
console.log("product mapping js start")

    // Function to load SIM records and refresh the table
    // function loadProducts() {
    //     $.get(quadcellProducts.ajax_url, { action: 'load_product' }, function(response) {
    //         if (response.success) {
    //             console.log(response.data)
    //             $('#product-option').html(response.data.html);
    //         } else {
    //             alert('Error: ' + response.data.message);
    //         }
    //     });
    // }

    // Load SIM records on page load
    // loadProducts();
    function loadProductsMapping() {
        $.get(quadcellProducts.ajax_url, { action: 'load_product_mapping' }, function(response) {
            console.log(response)
            if (response.success) {
                $('#product-map-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    }
    loadProductsMapping() 
    $('#product-map-form').on('submit', function(e) {
        console.log("product mapping js form submit")
        e.preventDefault();

        var formData = $(this).serializeArray();

        $.post( quadcellProducts.ajax_url,{
            action: 'save_product_mapping',
            product_name:formData[0].value,
            profile_name:formData[1].value,
            nonce: quadcellApiMapping.nonce,
      
        }, function(response) {
            console.log(response)
            if (response.success) {
                alert('Product mapping updated successfully.');
                loadProductsMapping()
                $('#default-product-option').prop('selected', true);
                $('#default-profile-option').prop('selected', true);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });
})