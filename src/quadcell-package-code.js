jQuery(document).ready(function($) {
    $('#add-new-package-code').on('click', function(e) {
        e.preventDefault();
        $('#add-package-code-form').toggle();
    });


    $('#cancel-add-package-code').on('click', function(e) {
        e.preventDefault();
        $('#add-package-code-form').hide();
    });


    $('#add-package-code-form').on('submit', function(e) {
        e.preventDefault();
        var data = {
            action: 'add_package_code',
            applicable_IMSI: $('#applicable_IMSI').val(),
            packCode: $('#packCode').val(),
            roaming_Profile: $('#roaming_Profile').val(),
            nonce: quadcellPackageCode.nonce
        };
        console.log('Sending Package data:', data); // Log data
        $.post(quadcellPackageCode.ajax_url, data, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });
});
