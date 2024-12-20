jQuery(document).ready(function($) {

    // Function to add a new mapping row
    function addMappingRow(exsistingData) {
        
        var table = $('#quadcell-api-plan-to-api-mappings-table tbody');
        var newIndex = table.find('tr').length;
        
        if(exsistingData){

       
  
            var prameter = prametersAPI(exsistingData.api_command,exsistingData.parameters,exsistingData.sequence);
            var newRow = '<tr data-id="" data-index="' + exsistingData.sequence + '"><td><select name="api_mappings[' + newIndex + '][api_command]" class="api-command-select">';
            newRow += '<option value="">-</option>'; // Default blank selection
      
            $.each(quadcellApiMapping.api_commands, function(command, fields) {
                if (exsistingData.api_command==command){newRow += '<option value="' + command + '" selected>' + command + '</option>';}else{newRow += '<option value="' + command + '">' + command + '</option>';}
            });

            newRow += '</select><div class="plan-code-info"></div></td><td><div class="api-parameters-container">'+prameter+'</div></td><td><input type="number" name="api_mappings[' + newIndex + '][sequence]" value="' + (newIndex + 1) + '" class="sequence-input" min="1" /></td><td><button type="button" class="button move-up">Up</button><button type="button" class="button move-down">Down</button><button type="button" class="button remove-mapping">Remove</button></td></tr>';
            
            }else{
                
            var newRow = '<tr data-id="" data-index="' + newIndex + '"><td><select name="api_mappings[' + newIndex + '][api_command]" class="api-command-select">';
        newRow += '<option value="">-</option>'; // Default blank selection
        $.each(quadcellApiMapping.api_commands, function(command, fields) {
  
            // if(exsistingData && exsistingData.data.mapping[0].api_commands===command){defaultSelected='selected="selected"'}else{defaultSelected=''}
            // console.log(exsistingData)
            newRow += '<option value="' + command + '">' + command + '</option>';
        
        });
        newRow += '</select><div class="plan-code-info"></div></td><td><div class="api-parameters-container"></div></td><td><input type="number" name="api_mappings[' + newIndex + '][sequence]" value="' + (newIndex + 1) + '" class="sequence-input" min="1" /></td><td><button type="button" class="button move-up">Up</button><button type="button" class="button move-down">Down</button><button type="button" class="button remove-mapping">Remove</button></td></tr>';

        // newRow += '</select></td><td><select name="api_mappings[' + newIndex + '][plan_code]" class="plan-code-select">';
   
        // $.each(quadcellApiMapping.plan_codes, function(i, plan) {
        //     newRow += '<option value="' + plan.planCode + '" ' + (plan.planCode === exsistingData ? 'selected' : '') + '>' + plan.planCode + '</option>';
        // });
        
            
    }
    table.append(newRow);
        
    }
    function prametersAPI(command,parameters,apiSequence){
        var fieldHTML =""
        encodePram = JSON.parse(parameters);
        sequence = apiSequence-1;
        // Fetch command parameters excluding IMSI, ICCID, and MSISDN
        let combinedObj = {};
        for (let key1 in quadcellApiMapping.api_commands[command]){
            for (let key2 in encodePram) {
                if (key1 === key2) {
                    combinedObj[key2] = encodePram[key2];
                }
                }
        }
       
        
        if (combinedObj) {
            $.each(combinedObj, function(field, value) {
                    fieldHTML += '<div><label>' + field + ':</label>';
                if(encodePram && field === 'planCode'){
                        fieldHTML += '<select name="api_mappings['+sequence+'][parameters][' + field + ']">';
                        $.each(quadcellApiMapping.plan_codes, function(i, plan) {
                            if(plan.planCode === value){
                                fieldHTML += '<option value="' + plan.planCode + '" selected>' + plan.planCode + '</option>';
                            }else{
                                fieldHTML += '<option value="' + plan.planCode + '">' + plan.planCode + '</option>';
                            }
                        }); 
                        fieldHTML += '</select>';
                } else if(encodePram && field === 'packCode'){
                        fieldHTML += '<select name="api_mappings['+sequence+'][parameters][' + field + ']">';
                        $.each(quadcellApiMapping.package_code, function(i, package) {
                            if(package.package_code === value){
                                fieldHTML += '<option value="' + package.package_code + '" selected>' + package.package_code + '</option>';
                            }else{
                                fieldHTML += '<option value="' + package.package_code + '">' + package.package_code + '</option>';
                            }
                        });
                        fieldHTML += '</select>';
                    
                }
                else if (encodePram){                   
                    fieldHTML += '<input type="' + (value.type === 'int' ? 'number' : 'text') + '" name="api_mappings['+sequence+'][parameters][' + field + ']" value="'+value+'"></div>';
                }else{
                    fieldHTML += '<input type="' + (value.type === 'int' ? 'number' : 'text') + '" name="api_mappings['+sequence+'][parameters][' + field + ']" value=""></div>';

                }
                

            });
        }

        return fieldHTML
    }
    // Bind event for adding new mapping, ensuring it is only bound once
    $('#add-plan-to-api-mapping').off('click').on('click', function() {
        addMappingRow(); // Adds a new row with a blank selection for plan code
    });

    // Handle profile selection and load mappings
    $('#selected_profile').on('change', function() {
        var profileName = $(this).val();

        $.post(quadcellApiMapping.ajax_url, {
            action: 'load_profile_mappings',
            profile_name: profileName,
            nonce: quadcellApiMapping.nonce
        }, function(response) {
         
            if (response.success) {
                $('#quadcell-api-plan-to-api-mappings-table tbody').empty(); // Clear existing rows
                $.each(response.data.mappings, function(index, mapping) {
                    // Load each mapping row with the correct plan code selected
   
                    addMappingRow(mapping); // Pass the selected plan code
                });
            } else {
                alert('Error loading profile mappings: ' + response.data.message);
            }
        });
    });

    // Remove mapping row
    $(document).on('click', '.remove-mapping', function() {
        $(this).closest('tr').remove();
        updateSequence();
    });

    // Handle saving mappings
    $('#quadcell-api-mappings-form').on('submit', function(e) {
        e.preventDefault();

        var selectedProfile = $('#selected_profile').val(); // Capture the selected profile
        var formData = $(this).serializeArray(); // Serialize form data as an array
     console.log(formData)
        // Convert serialized data to an object for easier manipulation
        var mappingsData = {
            selected_profile: selectedProfile,
            api_mappings: []
        };

        $.each(formData, function(_, field) {

    ;function extractParameter(fieldName) {
        const match = fieldName.match(/^api_mappings\[(\d+)]\[(\w+)](?:\[(\w+)])?/);
        
        return match ? match.slice(1).filter(Boolean) : null;
    }
        matchResult = extractParameter(field.name)

                var index = parseInt(matchResult[0]);
                var key = matchResult[1]
                if (!mappingsData.api_mappings[index] ) {
                    mappingsData.api_mappings[index] = {};
                }
                // Ensure mappingsData.api_mappings has an array entry at the current index
    
                if (key === 'api_command' || key === 'sequence') {
                    mappingsData.api_mappings[index][key] = field.value;
       
                } else if(key === 'parameters'){
                        var paramKey = matchResult[2];
                        if (!mappingsData.api_mappings[index].parameters) {
                            mappingsData.api_mappings[index].parameters = {};
                        }
                        mappingsData.api_mappings[index].parameters[paramKey]= field.value;
                                
                };
            
            
        });

        // Debugging: Display the mappings data to verify
        
   

        // Send formatted data to the server
      
        $.post(quadcellApiMapping.ajax_url, {
            action: 'save_api_mappings',
            data: JSON.stringify(mappingsData), // Convert to JSON string
            nonce: quadcellApiMapping.nonce
        }, function(response) {
            if (response.success) {
                alert('Mappings saved successfully.');
            } else {
                alert('Error saving mappings: ' + response.data.message);
            }
        });
    });

    // Move mapping row up and down
    $(document).on('click', '.move-up', function() {
        var row = $(this).closest('tr');
        row.prev().before(row);
        updateSequence();
    });

    $(document).on('click', '.move-down', function() {
        var row = $(this).closest('tr');
        row.next().after(row);
        updateSequence();
    });

    // Update sequence numbers
    function updateSequence() {
        $('#quadcell-api-plan-to-api-mappings-table tbody tr').each(function(index) {
            $(this).find('.sequence-input').val(index + 1); // Ensure sequence starts from 1
        });
    }


    // Update plan code information
    $(document).on('change', '.plan-code-select', function() {
        var planCode = $(this).val();
        var planInfoContainer = $(this).siblings('.plan-code-info');

        // Fetch plan code details via AJAX
        $.post(quadcellApiMapping.ajax_url, {
            action: 'fetch_plan_code_info',
            plan_code: planCode,
            nonce: quadcellApiMapping.nonce
        }, function(response) {
            if (response.success) {
                var planInfo = response.data.plan_info;
                planInfoContainer.html(
                    '<p><strong>Region:</strong> ' + planInfo.roaming_Region + '</p>' +
                    '<p><strong>Service:</strong> ' + planInfo.mobile_Service + '</p>' +
                    '<p><strong>Profile:</strong> ' + planInfo.roaming_Profile + '</p>' +
                    '<p><strong>Validity:</strong> ' + planInfo.validity_Mode + '</p>'
                );
            } else {
                planInfoContainer.html('<p>Error retrieving plan code details.</p>');
            }
        });
    });

    // Add new profile functionality
    $('#add-new-profile').click(function() {
        var profileName = prompt('Enter a new profile name:');
        if (profileName) {
            // Send AJAX request to add new profile
            $.post(quadcellApiMapping.ajax_url, {
                action: 'add_new_profile',
                profile_name: profileName,
                nonce: quadcellApiMapping.nonce
            }, function(response) {
                if (response.success) {
                    $('<option>').val(profileName).text(profileName).appendTo('#selected_profile');
                    $('#selected_profile').val(profileName).trigger('change');
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        }
    });

    // Fetch API command parameters on command change
    $(document).on('change', '.api-command-select', function() {

        var command = $(this).val();
        var parametersContainer = $(this).closest('tr').find('.api-parameters-container');
        var apiSequence = parseInt($(this).closest('tr').find('.sequence-input').val())-1;
        parametersContainer.empty();
 
        if (quadcellApiMapping.api_commands[command]) {
            $.each(quadcellApiMapping.api_commands[command], function(field, properties) {
       
                if (['imsi', 'iccid', 'msisdn'].indexOf(field) === -1) {
         
                    var fieldHTML = '<label>' + field + ':</label>';
                    if (field === 'planCode') {
                        fieldHTML += '<select name="api_mappings['+apiSequence+'][parameters][' + field + ']">';
                        $.each(quadcellApiMapping.plan_codes, function(i, plan) {
                            fieldHTML += '<option value="' + plan.planCode + '">' + plan.planCode + '</option>';
                        }); 
                        fieldHTML += '</select>';
                    } else if(field === 'packCode'){
                        fieldHTML += '<select name="api_mappings['+apiSequence+'][parameters][' + field + ']">';
                        $.each(quadcellApiMapping.package_code, function(i, plan) {
                            fieldHTML += '<option value="' + plan.package_code + '">' + plan.package_code + '</option>';
                        });
                        fieldHTML += '</select>';
                    } else {
                // if(paramters){
                //    fieldHTML += '<input type="' + (properties.type === 'int' ? 'number' : 'text') + '" name="api_mappings['+apiSequence+'][parameters][' + field + ']" value="'+paramters[field]+'">';
                // }else{
                    fieldHTML += '<input type="' + (properties.type === 'int' ? 'number' : 'text') + '" name="api_mappings['+apiSequence+'][parameters][' + field + ']" value="">';

                // }
                    }
                    ;
                    parametersContainer.append('<div>' + fieldHTML + '</div>');
                }
                
                
            });
        }

        
    });

    // Sortable rows for sequence management
    $('#quadcell-api-plan-to-api-mappings-table tbody').sortable({
        update: function(event, ui) {
            updateSequence();
        }
    });
});
