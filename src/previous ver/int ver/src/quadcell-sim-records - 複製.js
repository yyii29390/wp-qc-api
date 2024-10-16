jQuery(document).ready(function($) {
    function resetForm() {
        $('#record_id').val('');
        $('#imsi').val('');
        $('#iccid').val('');
        $('#msisdn').val('');
        $('#in_use').prop('checked', false);
        $('#add-sim-record-button').text('Add SIM Record');
    }

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
});
