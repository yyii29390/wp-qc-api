jQuery(document).ready(function($) {
    // Function to handle adding a SIM record
    $('#add-sim-record-button').on('click', function() {
        var data = {
            action: 'add_sim_record',
            imsi: $('#imsi').val(),
            iccid: $('#iccid').val(),
            msisdn: $('#msisdn').val(),
            in_use: $('#in_use').is(':checked') ? 1 : 0,
            nonce: quadcellSimRecords.nonce // Add nonce for security
        };

        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                $('#sim-records-table-body').append(
                    '<tr data-id="' + response.data.sim_record.id + '">' +
                    '<td>' + response.data.sim_record.id + '</td>' +
                    '<td>' + response.data.sim_record.imsi + '</td>' +
                    '<td>' + response.data.sim_record.iccid + '</td>' +
                    '<td>' + response.data.sim_record.msisdn + '</td>' +
                    '<td>' + (response.data.sim_record.in_use ? 'Yes' : 'No') + '</td>' +
                    '<td>' +
                    '<button class="delete-sim-record-button button" data-id="' + response.data.sim_record.id + '">Delete</button>' +
                    '<button class="edit-sim-record-button button" data-id="' + response.data.sim_record.id + '">Edit</button>' +
                    '</td>' +
                    '</tr>'
                );
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle deleting a SIM record
    $('#sim-records-table-body').on('click', '.delete-sim-record-button', function(event) {
        event.preventDefault(); // Prevent form submission if inside form
        var id = $(this).data('id');
        var data = {
            action: 'delete_sim_record',
            id: id,
            nonce: quadcellSimRecords.nonce
        };

        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                $('button[data-id="' + id + '"]').closest('tr').remove();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle editing a SIM record
    $('#sim-records-table-body').on('click', '.edit-sim-record-button', function(event) {
        event.preventDefault(); // Prevent form submission if inside form
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var imsi = row.find('td').eq(1).text();
        var iccid = row.find('td').eq(2).text();
        var msisdn = row.find('td').eq(3).text();
        var in_use = row.find('td').eq(4).text() === 'Yes';

        $('#imsi').val(imsi);
        $('#iccid').val(iccid);
        $('#msisdn').val(msisdn);
        $('#in_use').prop('checked', in_use);

        $('#add-sim-record-button').hide();
        $('#update-sim-record-button').remove();
        $('#add-sim-record-form').append('<button type="button" id="update-sim-record-button" class="button button-primary">Update SIM Record</button>');

        $('#update-sim-record-button').on('click', function() {
            var data = {
                action: 'update_sim_record',
                id: id,
                imsi: $('#imsi').val(),
                iccid: $('#iccid').val(),
                msisdn: $('#msisdn').val(),
                in_use: $('#in_use').is(':checked') ? 1 : 0,
                nonce: quadcellSimRecords.nonce
            };

            $.post(quadcellSimRecords.ajax_url, data, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    row.find('td').eq(1).text(data.imsi);
                    row.find('td').eq(2).text(data.iccid);
                    row.find('td').eq(3).text(data.msisdn);
                    row.find('td').eq(4).text(data.in_use ? 'Yes' : 'No');

                    $('#update-sim-record-button').remove();
                    $('#add-sim-record-button').show();
                    $('#add-sim-record-form')[0].reset();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });
    });

    // Function to handle exporting SIM records
    $('#export-sim-records-button').on('click', function() {
        window.location.href = quadcellSimRecords.export_url;
    });

    // Function to handle importing SIM records
    $('#import-sim-records-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: quadcellSimRecords.import_url,
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
    $('#search-sim-records-form').on('submit', function(e) {
        e.preventDefault();
        var searchData = $(this).serialize();

        $.get(quadcellSimRecords.search_url, searchData, function(response) {
            if (response.success) {
                // Update the table with the search results
                $('#sim-records-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });
});
