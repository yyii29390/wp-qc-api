jQuery(document).ready(function($) {
    let isEditMode = false; // Track if we are in edit mode
    
    // Initially hide the update and cancel buttons
    $('#update-sim-record-button, #cancel-edit-button').hide();

    // Function to load SIM records and refresh the table
    function loadSimRecords() {

        $.get(quadcellSimRecords.ajax_url, { action: 'load_sim_records' }, function(response) {
            if (response.success) {

                $('#sim-records-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    }

    // Load SIM records on page load
    loadSimRecords();

    // Function to handle adding a SIM record
    $('#add-sim-record-button').on('click', function() {
        if (isEditMode) {
            return; // Prevent adding while in edit mode
        }

        var data = {
            action: 'add_sim_record',
            imsi: $('#imsi').val(),
            iccid: $('#iccid').val(),
            msisdn: $('#msisdn').val(),
            in_use: $('#in_use').is(':checked') ? 1 : 0,
            nonce: quadcellSimRecords.nonce // Add nonce for security
        };
        
        console.log('Sending add data:', data); // Log data
        console.log(quadcellSimRecords.ajax_url)
        
        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            console.log('Add response:', response); // Log response
            if (response.success) {
                alert(response.data.message);
                loadSimRecords(); // Refresh the table data
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

        console.log('Sending delete data:', data); // Log data
        console.log(quadcellSimRecords.ajax_url)
        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            console.log('Delete response:', response); // Log response
            if (response.success) {
                alert(response.data.message);
                loadSimRecords(); // Refresh the table data
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle editing a SIM record
    $('#sim-records-table-body').on('click', '.edit-sim-record-button', function(event) {
        event.preventDefault(); // Prevent form submission if inside form
        isEditMode = true; // Set edit mode to true

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
        $('#update-sim-record-button, #cancel-edit-button').show().data('id', id);
    });

    // Function to handle updating a SIM record
    $('#update-sim-record-button').on('click', function() {
        var id = $(this).data('id');
        var data = {
            action: 'update_sim_record',
            id: id,
            imsi: $('#imsi').val(),
            iccid: $('#iccid').val(),
            msisdn: $('#msisdn').val(),
            in_use: $('#in_use').is(':checked') ? 1 : 0,
            nonce: quadcellSimRecords.nonce
        };

        console.log('Sending update data:', data); // Log data

        $.post(quadcellSimRecords.ajax_url, data, function(response) {
            console.log('Update response:', response); // Log response
            if (response.success) {
                alert(response.data.message);
                $('#update-sim-record-button, #cancel-edit-button').hide();
                $('#add-sim-record-button').show();
                // $('#add-sim-record-form')[0].reset();
                isEditMode = false; // Reset edit mode
                loadSimRecords(); // Refresh the table data
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Function to handle cancel button click
    $('#cancel-edit-button').on('click', function() {
        $('#update-sim-record-button, #cancel-edit-button').hide();
        $('#add-sim-record-button').show();
        $('#add-sim-record-form')[0].reset();
        isEditMode = false; // Reset edit mode
                loadSimRecords(); // Refresh the table data		
    });

    // Function to handle exporting SIM records
    $('#export-sim-records-button').on('click', function() {
        window.location.href = quadcellSimRecords.export_url;
    });

    // Function to handle importing SIM records
    $('#import-sim-records-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        console.log('Importing data:', formData); // Log data

        $.ajax({
            url: quadcellSimRecords.import_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Import response:', response); // Log response
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

        console.log('Searching data:', searchData); // Log data

        $.get(quadcellSimRecords.search_url, searchData, function(response) {
            console.log('Search response:', response); // Log response
            if (response.success) {
                // Update the table with the search results
                $('#sim-records-table-body').html(response.data.html);
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });
});
