jQuery(document).ready(function($) {
    $('#add-plan-code-button').on('click', function() {
        var data = {
            action: 'add_plan_code',
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
                $('#plan-codes-table-body').append(
                    '<tr>' +
                    '<td>' + response.data.plan_code.id + '</td>' +
                    '<td>' + response.data.plan_code.applicable_IMSI + '</td>' +
                    '<td>' + response.data.plan_code.planCode + '</td>' +
                    '<td>' + response.data.plan_code.roaming_Region + '</td>' +
                    '<td>' + response.data.plan_code.mobile_Service + '</td>' +
                    '<td>' + response.data.plan_code.roaming_Profile + '</td>' +
                    '<td>' + response.data.plan_code.validity_Mode + '</td>' +
                    '<td>' +
                    '<button class="delete-plan-code-button button" data-id="' + response.data.plan_code.id + '">Delete</button>' +
                    '<button class="edit-plan-code-button button" data-id="' + response.data.plan_code.id + '">Edit</button>' +
                    '</td>' +
                    '</tr>'
                );
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    $('#plan-codes-table-body').on('click', '.delete-plan-code-button', function() {
        var id = $(this).data('id');
        var data = {
            action: 'delete_plan_code',
            id: id
        };

        $.post(quadcellPlanCode.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                $('button[data-id="' + id + '"]').closest('tr').remove();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    $('#plan-codes-table-body').on('click', '.edit-plan-code-button', function() {
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
        $('#update-plan-code-button').remove();
        $('#add-plan-code-form').append('<button type="button" id="update-plan-code-button" class="button button-primary">Update Plan Code</button>');

        $('#update-plan-code-button').on('click', function() {
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
                    row.find('td').eq(1).text(data.applicable_IMSI);
                    row.find('td').eq(2).text(data.planCode);
                    row.find('td').eq(3).text(data.roaming_Region);
                    row.find('td').eq(4).text(data.mobile_Service);
                    row.find('td').eq(5).text(data.roaming_Profile);
                    row.find('td').eq(6).text(data.validity_Mode);

                    $('#update-plan-code-button').remove();
                    $('#add-plan-code-button').show();
                    $('#add-plan-code-form')[0].reset();
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
	
});
