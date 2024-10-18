jQuery(document).ready(function($) {
    let isEditMode = false; 
    $('#update-package-code-button, #cancel-edit-button').hide();
    function loadPackageCodeRecords() {
        $.get(quadcellPackageCode.ajax_url, { action: 'load_package_code' }, function(response) {
            if (response.success) {
                $('#package-codes-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    }
    
    $('#add-package-code-button').on('click', function() {
        if (isEditMode) {
            return; // Prevent adding while in edit mode
        }
        var data = {
            action: 'add_package_code',
            applicable_IMSI: $('#applicable_IMSI').val(),        
            package_Code: $('#package_Code').val(),
            preset_Data_Volume: $('#preset_Data_Volume').val(),
            validity_Mode: $('#validity_Mode').val(),
            FUP_Mode: $('#FUP_Mode').val(),
            roaming_Region: $('#roaming_Region').val(),
            roaming_Country: $('#roaming_Country').val(),
            nonce: quadcellPackageCode.nonce
        };
        $.post(quadcellPackageCode.ajax_url, data, function(response) {
        
            if (response.success) {
                alert(response.data.message);
                loadPackageCodeRecords();
                $('#applicable_IMSI').val("");
                $('#package_Code').val("");
                $('#preset_Data_Volume').val("");
                $('#validity_Mode').val("") ;
                $('#FUP_Mode').val("") ;
                $('#roaming_Region').val("") ;
                $('#roaming_Country').val("") ;
            } else {
                console.log("Error show")
                alert('Error: ' + response.data.message);
            }
        });
    });

    // $('#package-codes-table-body').on('click', '.delete-package-code-button', function() {
    //     var id = $(this).data('id');
    //     var data = {
    //         action: 'delete_package_code',
    //         id: id
    //     };

    //     $.post(quadcellPackageCode.ajax_url, data, function(response) {
    //         if (response.success) {
    //             alert(response.data.message);
    //             $('button[data-id="' + id + '"]').closest('tr').remove();
    //         } else {
    //             alert('Error: ' + response.data.message);
    //         }
    //     });
    // });

    $('#package-codes-table-body').on('click', '.edit-package-code-button', function(event) {
        event.preventDefault(); // Prevent form submission if inside form
        isEditMode = true; // Set edit mode to true
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var applicable_IMSI = row.find('td').eq(1).text();
        var package_Code = row.find('td').eq(2).text();
        var preset_Data_Volume = row.find('td').eq(3).text();
        var validity_Mode = row.find('td').eq(4).text();
        var FUP_Mode = row.find('td').eq(5).text();
        var roaming_Region = row.find('td').eq(6).text();
        var roaming_Country = row.find('td').eq(7).text();


        $('#applicable_IMSI').val(applicable_IMSI);
        $('#package_Code').val(package_Code);
    $('#preset_Data_Volume').val(preset_Data_Volume);
        $('#validity_Mode').val(validity_Mode);
        $('#FUP_Mode').val(FUP_Mode);
        $('#roaming_Region').val(roaming_Region);
        $('#roaming_Country').val(roaming_Country);


        $('#add-package-code-button').hide();
        // $('#update-package-code-button').remove();
        $('#update-package-code-button, #cancel-edit-button').show().data('id', id);
        $('#add-package-code-form').append('<button type="button" id="update-package-code-button" class="button button-primary">Update Plan Code</button>');

        $('#update-package-code-button').on('click', function() {
            var id = $(this).data('id');
            var data = {
                action: 'update_package_code',
                id: id,
                applicable_IMSI: $('#applicable_IMSI').val(),
                package_Code: $('#package_Code').val(),
                preset_Data_Volume: $('#preset_Data_Volume').val(),
                validity_Mode: $('#validity_Mode').val(),
                FUP_Mode: $('#FUP_Mode').val(),
                roaming_Region: $('#roaming_Region').val(),
                roaming_Country: $('#roaming_Country').val(),
            };

            $.post(quadcellPackageCode.ajax_url, data, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    // row.find('td').eq(1).text(data.applicable_IMSI);
                    // row.find('td').eq(2).text(data.package_Code);
                    // row.find('td').eq(3).text(data.roaming_Region);
                    // row.find('td').eq(4).text(data.mobile_Service);
                    // row.find('td').eq(5).text(data.roaming_Profile);
                    // row.find('td').eq(6).text(data.validity_Mode);
                    $('#applicable_IMSI').val("");
                    $('#package_Code').val("");
                    $('#preset_Data_Volume').val("");
                    $('#validity_Mode').val("") ;
                    $('#FUP_Mode').val("") ;
                    $('#roaming_Region').val("") ;
                    $('#roaming_Country').val("") ;
                    $('#update-package-code-button').remove();
                    $('#add-package-code-button').show();
                    isEditMode = false;
                    loadPackageCodeRecords();
                    // $('#add-package-code-form')[0].reset();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });
    });
	// Function to handle exporting plan codes
	$('#export-package-codes-button').on('click', function() {
		window.location.href = quadcellPackageCode.export_url;
	});

	// Function to handle importing plan codes
	$('#import-package-codes-form').on('submit', function(e) {
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: quadcellPackageCode.import_url,
			type: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			success: function(response) {
				if (response.success) {
					alert('Import successful');
					location.reload();
				} else {
					alert('Error: ' + response.data.message);
				}
			}
		});
	});
	// Function to handle search and filtering
	$('#search-package-codes-form').on('submit', function(e) {
		e.preventDefault();
		var searchData = $(this).serialize();

		$.get(quadcellPackageCode.search_url, searchData, function(response) {
			if (response.success) {
				// Update the table with the search results
				$('#package-codes-table-body').html(response.data.html);
			} else {
				alert('Error: ' + response.data.message);
			}
		});
	});
    $('#package-codes-table-body').on('click', '.delete-package-code-button', function(event) {
        if(confirm('Are you sure you want to delete this plan code?')){
            event.preventDefault(); // Prevent form submission if inside form
            var id = $(this).data('id');
            var data = {
                action: 'delete_package_code',
                id: id,
                nonce: quadcellPackageCode.nonce
            };
    
            console.log('Sending delete data:', data); // Log data
    
            $.post(quadcellPackageCode.ajax_url, data, function(response) {
                console.log('Delete response:', response); // Log response
                if (response.success) {
                    alert(response.data.message);
                    loadPackageCodeRecords(); // Refresh the table data
                } else {
                    alert('Error: ' + response.data.message);
                }
            });}
        
    });
 
});
