jQuery(document).ready(function($) {
    let isEditMode = false; 
    $('#update-plan-code-button, #cancel-edit-button').hide();
    function loadPlanCodeRecords() {
        $.get(quadcellPlanCode.ajax_url, { action: 'load_plan_code' }, function(response) {
            if (response.success) {
                $('#plan-codes-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    }
    
    $('#add-plan-code-button').on('click', function() {
        if (isEditMode) {
            return; // Prevent adding while in edit mode
        }
        var data = {
            action: 'add_plan_code',
            applicable_IMSI: $('#applicable_IMSI').val(),        
            planCode: $('#planCode').val(),
            roaming_Region: $('#roaming_Region').val(),
            mobile_Service: $('#mobile_Service').val(),
            roaming_Profile: $('#roaming_Profile').val(),
            validity_Mode: $('#validity_Mode').val(),
            nonce: quadcellPlanCode.nonce
        };
        $.post(quadcellPlanCode.ajax_url, data, function(response) {
        
            if (response.success) {
                alert(response.data.message);
                loadPlanCodeRecords()
                $('#applicable_IMSI').val("");
                $('#planCode').val("");
                $('#roaming_Region').val("") ;
                $('#mobile_Service').val("") ;
                $('#roaming_Profile').val("") ;
                $('#validity_Mode').val("") ;

            } else {
                console.log("Error show")
                alert('Error: ' + response.data.message);
            }
        });
    });

    // $('#plan-codes-table-body').on('click', '.delete-plan-code-button', function() {
    //     var id = $(this).data('id');
    //     var data = {
    //         action: 'delete_plan_code',
    //         id: id
    //     };

    //     $.post(quadcellPlanCode.ajax_url, data, function(response) {
    //         if (response.success) {
    //             alert(response.data.message);
    //             $('button[data-id="' + id + '"]').closest('tr').remove();
    //         } else {
    //             alert('Error: ' + response.data.message);
    //         }
    //     });
    // });

    $('#plan-codes-table-body').on('click', '.edit-plan-code-button', function(event) {
        event.preventDefault(); // Prevent form submission if inside form
        isEditMode = true; // Set edit mode to true
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var applicable_IMSI = row.find('td').eq(1).text();
        var planCode = row.find('td').eq(2).text();
        var roaming_Region = row.find('td').eq(3).text();
        var mobile_Service = row.find('td').eq(4).text();
        var roaming_Profile = row.find('td').eq(5).text();
        var validity_Mode = row.find('td').eq(6).text();

        $('#applicable_IMSI').val(applicable_IMSI);
        $('#planCode').val(planCode);
        $('#roaming_Region').val(roaming_Region);
        $('#mobile_Service').val(mobile_Service);
        $('#roaming_Profile').val(roaming_Profile);
        $('#validity_Mode').val(validity_Mode);

        $('#add-plan-code-button').hide();
        // $('#update-plan-code-button').remove();
        $('#update-plan-code-button, #cancel-edit-button').show().data('id', id);
        $('#add-plan-code-form').append('<button type="button" id="update-plan-code-button" class="button button-primary">Update Plan Code</button>');

        $('#update-plan-code-button').on('click', function() {
            var id = $(this).data('id');
            var data = {
                action: 'update_plan_code',
                id: id,
                applicable_IMSI: $('#applicable_IMSI').val(),
                planCode: $('#planCode').val(),
                roaming_Region: $('#roaming_Region').val(),
                mobile_Service: $('#mobile_Service').val(),
                roaming_Profile: $('#roaming_Profile').val(),
                validity_Mode: $('#validity_Mode').val(),
            };

            $.post(quadcellPlanCode.ajax_url, data, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    // row.find('td').eq(1).text(data.applicable_IMSI);
                    // row.find('td').eq(2).text(data.planCode);
                    // row.find('td').eq(3).text(data.roaming_Region);
                    // row.find('td').eq(4).text(data.mobile_Service);
                    // row.find('td').eq(5).text(data.roaming_Profile);
                    // row.find('td').eq(6).text(data.validity_Mode);
                    $('#applicable_IMSI').val("");
                    $('#planCode').val("");
                    $('#roaming_Region').val("") ;
                    $('#mobile_Service').val("") ;
                    $('#roaming_Profile').val("") ;
                    $('#validity_Mode').val("") ;
                    $('#update-plan-code-button').remove();
                    $('#add-plan-code-button').show();
                    isEditMode = false;
                    loadPlanCodeRecords()
                    // $('#add-plan-code-form')[0].reset();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });
    });
	// Function to handle exporting plan codes
	$('#export-plan-codes-button').on('click', function() {
		window.location.href = quadcellPlanCode.export_url;
	});

	// Function to handle importing plan codes
	$('#import-plan-codes-form').on('submit', function(e) {
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: quadcellPlanCode.import_url,
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
	$('#search-plan-codes-form').on('submit', function(e) {
		e.preventDefault();
		var searchData = $(this).serialize();

		$.get(quadcellPlanCode.search_url, searchData, function(response) {
			if (response.success) {
				// Update the table with the search results
				$('#plan-codes-table-body').html(response.data.html);
			} else {
				alert('Error: ' + response.data.message);
			}
		});
	});
    $('#plan-codes-table-body').on('click', '.delete-plan-code-button', function(event) {
        if(confirm('Are you sure you want to delete this plan code?')){
            event.preventDefault(); // Prevent form submission if inside form
            var id = $(this).data('id');
            var data = {
                action: 'delete_plan_code',
                id: id,
                nonce: quadcellPlanCode.nonce
            };
    
            console.log('Sending delete data:', data); // Log data
    
            $.post(quadcellPlanCode.ajax_url, data, function(response) {
                console.log('Delete response:', response); // Log response
                if (response.success) {
                    alert(response.data.message);
                    loadPlanCodeRecords(); // Refresh the table data
                } else {
                    alert('Error: ' + response.data.message);
                }
            });}
        
    });
 
});
