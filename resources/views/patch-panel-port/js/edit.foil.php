<script>
    function setToday(inputName){
        $("#"+inputName).val($("#date").val());
    }

    var notesIntro = "### <?= date("Y-m-d" ) . ' - ' .$t->user->getUsername() ?> \n\n";

    $(document).ready(function() {
        var new_notes_set = false;
        var new_private_notes_set = false;
        var val_notes_loading = $('#notes').text();
        var val_private_notes_loading = $('#private_notes').text();

        $('.help-block').hide();

        if($('#switch_port').val() != null){
            setCustomer();
        }

        $('#duplex').change(function(){
            if(this.checked){
                $("#duplex-port-area").show();
            }
            else{
                $("#duplex-port-area").hide();
            }
        });

        if(<?= (int)$t->hasDuplex ?> ){
            $('#duplex').click();
        }

        $("#number").prop('readonly', true);
        $("#patch_panel").prop('readonly', true);
        $("#last_state_change_at").prop('readonly', true);


        $("#switch").change(function(){
            setSwitchPort();
        });


        $("#switch_port").change(function(){
            setCustomer();
            <?php if($t->allocating): ?>
            if($("#switch_port").val() != ''){
                switchPortId = $("#switch_port").val();
                $.ajax( "<?= url('/api/v4/switch-port') ?>/" + switchPortId + "/physical-interface" )
                    .done( function( data ) {
                        if( data.physicalInterfaceFound ) {
                            $("#pi_status_area").show();
                        } else {
                            $("#pi_status_area").hide();
                        }
                    })
                    .fail( function() {
                        alert("Error running ajax query for switch-port/$id/physical-interface");
                        $("#customer").html("");
                    })
            }
            <?php endif; ?>
        });

        function setSwitchPort(){
            $("#switch_port").html("<option value=\"\">Loading please wait</option>\n").trigger("chosen:updated");
            switchId = $("#switch").val();
            customerId = $("#customer").val();
            switchPortId = $("#switch_port_id").val();

            $.ajax( "<?= url('/api/v4/switcher')?>/" + switchId + "/switch-port", {
                data: {
                    switchId: switchId,
                    custId: $("#customer").val(),
                    spId: $("#switch_port_id").val()
                },
                type: 'POST'
            })
                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each(data.listPorts,function(key, value){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $("#switch_port").html(options);
                })
                .fail( function() {
                    throw new Error("Error running ajax query for api/v4/switcher/$id/switch-port");
                    alert("Error running ajax query for switcher/$id/customer/$custId/switch-port/$spId");
                    $("#customer").html("");
                })
                .always( function() {
                    $("#switch_port").trigger("chosen:updated");
                });

        }

        function setCustomer(){
            if($("#switch").val() != ''){
                var switchPortId = $("#switch_port").val();
                $("#customer").html("<option value=\"\">Loading please wait</option>\n");
                $("#customer").trigger("chosen:updated");
                $.ajax( "<?= url('/api/v4/switch-port') ?>/" + switchPortId + "/customer" )
                    .done( function( data ) {
                        if( data.customerFound ) {
                            $("#customer").html( '<option value="' + data.id + '">' + data.name + "</option>\n" );
                        } else {
                            $("#customer").html("");
                        }
                    })
                    .fail( function() {
                        alert("Error running ajax query for switch-port/$id/customer");
                        $("#customer").html("");
                    })
                    .always( function() {
                        $("#customer").trigger("chosen:updated");
                    });
            }
        }

        $("#customer").change(function(){
            $("#switch").html("<option value=\"\">Loading please wait</option>\n").trigger("chosen:updated");
            $("#switch_port").html("").trigger("chosen:updated");
            customerId = $("#customer").val();

            $.ajax( "<?= url('/api/v4/customer')?>/" + customerId + "/switches", {
                data: {
                    customerId: customerId,
                    patch_panel_id: $("#patch_panel_id").val()
                },
                type: 'POST'
            })
            .done( function( data ) {
                if(data.switchesFound){
                    var options = "<option value=\"\">Choose a switch</option>\n";
                    $.each(data.switches,function(key, value){
                        options += "<option value=\"" + key + "\">" + value + "</option>\n";
                    });
                    $("#switch").html(options);
                }
                else{
                    $("#switch").html("");
                }
            })
            .fail( function() {
                throw new Error("Error running ajax query for api/v4/customer/$id/switches");
                alert("Error running ajax query for api/v4/customer/$id/switches");
            })
            .always( function() {
                $("#switch").trigger("chosen:updated");
            });

        });


        function resetCustomer(){
            options = "<option value=''> Choose a customer</option>\n";
            <?php foreach ($t->customers as $id => $customer): ?>
                customer = '<?= $customer ?>';
                options += "<option value=\"" + <?= $id ?> + "\">" + customer  + "</option>\n";
            <?php endforeach; ?>
            $("#customer").html(options).trigger("chosen:updated");
        }


        $(".reset-btn").click(function(){
            options = "<option value=''> Choose a Switch</option>\n";
            <?php foreach ($t->switches as $id => $switch): ?>
                $switch = '<?= $switch ?>';
                options += "<option value=\"" + <?= $id ?> + "\">" + $switch  + "</option>\n";
            <?php endforeach; ?>
            $("#switch").html(options).trigger("chosen:updated");
            $("#switch_port").html('').trigger("chosen:updated");
            resetCustomer();
            $("#pi_status_area").hide();
        });




        $( "#help-btn" ).click( function() {
            if($( ".help-block" ).css('display') == 'none'){
                $( ".help-block" ).show();
            }
            else{
                $( ".help-block" ).hide();
            }

        });




        $('#notes').click(function(){
            notesSetDateUser('notes');
        });


        $('#private_notes').click(function(){
            notesSetDateUser('private_notes');
        });

        $('#notes').blur(function(){
            noteBlur('notes');
        });

        $('#private_notes').blur(function(){
            noteBlur('private_notes');
        });

        function notesSetDateUser(input){
            val_textarea = $('#'+input).text();
            pos = notesIntro.length + ($('#'+input).val().length - $('#'+input).text().length);

            if(val_textarea == '' ){
                $('#'+input).text(notesIntro);
                $('#'+input).setCursorPosition(pos);
            }
            else{
                if($('#'+input).text() != notesIntro){
                    if(input == 'notes'){
                        if(!new_notes_set){
                            $('#'+input).text(notesIntro+'\n\n'+val_textarea);
                            new_notes_set = true;
                            $('#'+input).setCursorPosition(pos);
                        }
                    }
                    else{
                        if(!new_private_notes_set){
                            $('#'+input).text(notesIntro+'\n\n'+val_textarea);
                            new_private_notes_set = true;
                            $('#'+input).setCursorPosition(pos);
                        }
                    }

                }

            }
        }

        function noteBlur(input){
            if($('#'+input).text() == $('#'+input).val()){
                if(input == 'notes') {
                    $('#' + input).text(val_notes_loading);
                    new_notes_set = false;
                }
                else{
                    $('#' + input).text(val_private_notes_loading);
                    new_private_notes_set = false;
                }
            }
        }


    });
</script>