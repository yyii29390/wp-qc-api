jQuery(document).ready(function($) {
    $('#quadcell-api-command').on('change', function() {
		var command = $(this).val();
		var fieldsContainer = $('#quadcell-api-fields');
		fieldsContainer.empty();
		var fields = window.quadcellApiCommands;
        if (fields[command]) {
            $.each(fields[command], function(field, properties) {
                var fieldHTML = '<div><label>' + field + ':</label>';
                fieldHTML += '<input type="' + (properties.type === 'int' ? 'number' : 'text') + '" name="' + field + '"';
                if (properties.required) {
                    fieldHTML += ' required';
                }
                fieldHTML += '></div>';
                fieldsContainer.append(fieldHTML);
            });
        }
    });

    $('#quadcell-api-submit').on('click', function() {
        var command = $('#quadcell-api-command').val();
        var fields = {};
        $('#quadcell-api-fields input').each(function() {
            var value = $(this).val();
            if (value !== '') {
                fields[$(this).attr('name')] = value;
            }
        });

        fields = { 'authKey': quadcellApiSettings.auth_key, ...fields };

        var nonEncryptedJson = JSON.stringify(fields);

        $('#quadcell-api-full-url').text(quadcellApiSettings.url + '/' + command);
        $('#quadcell-api-non-encrypted-json').text(nonEncryptedJson);

        $.post(quadcellApiSettings.ajax_url, {
            action: 'quadcell_api_call',
            command: command,
            fields: fields
        }, function(response) {
            if (response.success) {
                $('#quadcell-api-encrypted-json').text(response.data.final_encrypted_data); // display final encrypted data
                $('#quadcell-api-result-message').html(
                    '<p><strong>Encrypted Response Body:</strong> ' + response.data.encrypted_data + '</p>' +
                    '<p><strong>Decrypted Result:</strong> ' + JSON.stringify(response.data.decrypted_data) + '</p>'
                );
            } else {
                $('#quadcell-api-result-message').html('<p><strong>Error Message:</strong> ' + response.data + '</p>');
            }
        });
    });
	
	
    // Function to handle the form reset
    function resetForm() {
        $('#record_id').val('');
        $('#imsi').val('');
        $('#iccid').val('');
        $('#msisdn').val('');
        $('#in_use').prop('checked', false);
        $('#add-sim-record-button').text('Add SIM Record');
    }

    // Function to handle adding or editing a SIM record
    $('#add-sim-record-button').on('click', function() {
        var action = $('#record_id').val() ? 'edit_sim_record' : 'add_sim_record';
        var data = {
            action: action,
            id: $('#record_id').val(),
            imsi: $('#imsi').val(),
            iccid: $('#iccid').val(),
            msisdn: $('#msisdn').val(),
            in_use: $('#in_use').is(':checked') ? 1 : 0,
        };

        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            if (response.success) {
                var record = response.data.sim_record;
                if (action === 'add_sim_record') {
                    var newRow = '<tr data-id="' + record.id + '">';
                    newRow += '<td>' + record.id + '</td>';
                    newRow += '<td>' + record.imsi + '</td>';
                    newRow += '<td>' + record.iccid + '</td>';
                    newRow += '<td>' + record.msisdn + '</td>';
                    newRow += '<td>' + (record.in_use ? 'Yes' : 'No') + '</td>';
                    newRow += '<td>';
                    newRow += '<button class="button edit-sim-record-button" data-id="' + record.id + '">Edit</button> ';
                    newRow += '<button class="button delete-sim-record-button" data-id="' + record.id + '">Delete</button>';
                    newRow += '</td>';
                    newRow += '</tr>';
                    $('#sim-records-table-body').append(newRow);
                } else {
                    var row = $('tr[data-id="' + record.id + '"]');
                    row.find('td:nth-child(2)').text(record.imsi);
                    row.find('td:nth-child(3)').text(record.iccid);
                    row.find('td:nth-child(4)').text(record.msisdn);
                    row.find('td:nth-child(5)').text(record.in_use ? 'Yes' : 'No');
                }
                resetForm();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle deleting a SIM record
    $('#sim-records-table-body').on('click', '.delete-sim-record-button', function() {
        if (!confirm('Are you sure you want to delete this SIM record?')) {
            return;
        }

        var recordId = $(this).data('id');
        var row = $(this).closest('tr');

        $.post(quadcellSimRecords.ajax_url, { action: 'delete_sim_record', id: recordId }, function(response) {
            if (response.success) {
                row.remove();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle editing a SIM record
    $('#sim-records-table-body').on('click', '.edit-sim-record-button', function() {
        var recordId = $(this).data('id');
        var row = $(this).closest('tr');
        var imsi = row.find('td:nth-child(2)').text();
        var iccid = row.find('td:nth-child(3)').text();
        var msisdn = row.find('td:nth-child(4)').text();
        var in_use = row.find('td:nth-child(5)').text() === 'Yes';

        $('#record_id').val(recordId);
        $('#imsi').val(imsi);
        $('#iccid').val(iccid);
        $('#msisdn').val(msisdn);
        $('#in_use').prop('checked', in_use);
        $('#add-sim-record-button').text('Update SIM Record');
    });

    // Function to handle importing SIM records
    $('#import-sim-records-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: quadcellSimRecords.ajax_url,
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

    // Function to handle the Plan Code form reset
    function resetPlanCodeForm() {
        $('#plan_code_id').val('');
        $('#applicable_imsi').val('');
        $('#plan_code').val('');
        $('#roaming_region').val('');
        $('#mobile_service').val('');
        $('#roaming_profile').val('');
        $('#validity_mode').val('');
        $('#add-plan-code-button').text('Add Plan Code');
    }

    // Function to handle adding or editing a Plan Code
    $('#add-plan-code-button').on('click', function() {
        var action = $('#plan_code_id').val() ? 'edit_plan_code' : 'add_plan_code';
        var data = {
            action: action,
            id: $('#plan_code_id').val(),
            applicable_imsi: $('#applicable_imsi').val(),
            plan_code: $('#plan_code').val(),
            roaming_region: $('#roaming_region').val(),
            mobile_service: $('#mobile_service').val(),
            roaming_profile: $('#roaming_profile').val(),
            validity_mode: $('#validity_mode').val(),
        };

        $.post(quadcellPlanCode.ajax_url, data, function(response) {
            if (response.success) {
                var record = response.data.plan_code;
                if (action === 'add_plan_code') {
                    var newRow = '<tr data-id="' + record.id + '">';
                    newRow += '<td>' + record.id + '</td>';
                    newRow += '<td>' + record.applicable_imsi + '</td>';
                    newRow += '<td>' + record.plan_code + '</td>';
                    newRow += '<td>' + record.roaming_region + '</td>';
                    newRow += '<td>' + record.mobile_service + '</td>';
                    newRow += '<td>' + record.roaming_profile + '</td>';
                    newRow += '<td>' + record.validity_mode + '</td>';
                    newRow += '<td>';
                    newRow += '<button class="button edit-plan-code-button" data-id="' + record.id + '">Edit</button> ';
                    newRow += '<button class="button delete-plan-code-button" data-id="' + record.id + '">Delete</button>';
                    newRow += '</td>';
                    newRow += '</tr>';
                    $('#plan-code-table-body').append(newRow);
                } else {
                    var row = $('tr[data-id="' + record.id + '"]');
                    row.find('td:nth-child(2)').text(record.applicable_imsi);
                    row.find('td:nth-child(3)').text(record.plan_code);
                    row.find('td:nth-child(4)').text(record.roaming_region);
                    row.find('td:nth-child(5)').text(record.mobile_service);
                    row.find('td:nth-child(6)').text(record.roaming_profile);
                    row.find('td:nth-child(7)').text(record.validity_mode);
                }
                resetPlanCodeForm();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle deleting a Plan Code
    $('#plan-code-table-body').on('click', '.delete-plan-code-button', function() {
        if (!confirm('Are you sure you want to delete this Plan Code?')) {
            return;
        }

        var recordId = $(this).data('id');
        var row = $(this).closest('tr');

        $.post(quadcellPlanCode.ajax_url, { action: 'delete_plan_code', id: recordId }, function(response) {
            if (response.success) {
                row.remove();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle editing a Plan Code
    $('#plan-code-table-body').on('click', '.edit-plan-code-button', function() {
        var recordId = $(this).data('id');
        var row = $(this).closest('tr');
        var applicable_imsi = row.find('td:nth-child(2)').text();
        var plan_code = row.find('td:nth-child(3)').text();
        var roaming_region = row.find('td:nth-child(4)').text();
        var mobile_service = row.find('td:nth-child(5)').text();
        var roaming_profile = row.find('td:nth-child(6)').text();
        var validity_mode = row.find('td:nth-child(7)').text();

        $('#plan_code_id').val(recordId);
        $('#applicable_imsi').val(applicable_imsi);
        $('#plan_code').val(plan_code);
        $('#roaming_region').val(roaming_region);
        $('#mobile_service').val(mobile_service);
        $('#roaming_profile').val(roaming_profile);
        $('#validity_mode').val(validity_mode);
        $('#add-plan-code-button').text('Update Plan Code');
    });
});


