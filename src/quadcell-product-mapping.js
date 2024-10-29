jQuery(document).ready(function($) {
console.log("product mapping js start")

    // Function to load SIM records and refresh the table
    function loadProducts() {
        $.get(quadcellProducts.ajax_url, { action: 'load_product' }, function(response) {
            if (response.success) {
                console.log(response.data)
                $('#product-option').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    }

    // Load SIM records on page load
    loadProducts();
    $('#product-map-form').on('submit', function(e) {
        e.preventDefault();
    });
})