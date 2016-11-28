<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>Route Search</small>
<?php $this->append() ?>

<?php $this->section('content') ?>


<form class="form-horizontal">
    <div class="form-group">
        <label for="net" class="col-sm-2 control-label">IP Address/Prefix</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="net" placeholder="192.0.2.0/24  |  2001:db8:7:2::/64">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <label class="radio-inline" class="control-label">
                <input type="radio" name="sourceSelector" id="sourceSelector-table"    value="table" checked="checked"> Lookup table
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <label class="radio-inline" class="col-sm-10 col-sm-offset-2 control-label">
                <input type="radio" name="sourceSelector" id="sourceSelector-protocol" value="protocol"> Lookup protocol
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="source" class="col-sm-2 control-label">Source</label>
        <div class="col-sm-10">
            <select class="form-control" id="source">
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button id="submit" type="button" class="btn btn-default">Submit</button>
        </div>
    </div>
</form>


<div class="modal fade" id="route-modal" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>


<?php $this->append() ?>

<?php $this->section('scripts') ?>

<script type="text/javascript">


var tables    = {!! json_encode($content->symbols->{'routing table'}) !!}.sort();
var protocols = {!! json_encode($content->symbols->protocol) !!}.sort();
var source    = 'table';

$("#submit").on( 'click', function(){
    if( $("#net").val().trim() == "" ) {
        return;
    }
    $("#submit").prop('disabled', true);

    $.get('{{$url}}/lg/route/' + encodeURIComponent($("#net").val().trim()) + '/' +
            source + '/' + encodeURIComponent( $("#source").val() ), function(html) {
        $('#route-modal .modal-content').html(html);
        $('#route-modal').modal('show', {backdrop: 'static'});
     });

    $("#submit").prop('disabled', false);
});

$('input:radio[name="sourceSelector"]').change( function(){
    if( $(this).is(':checked') ) {
        if( $(this).val() == "table" ) {
            source = 'table'
            $("#source").html("");
            tables.forEach( function(e){
                $("#source").append( $("<option></option>")
                    .attr("value",e)
                    .text(e)
                );
            });
            $("#source").val('master');
        } else {
            source = 'protocol'
            $("#source").html("");
            protocols.forEach( function(e){
                $("#source").append( $("<option></option>")
                    .attr("value",e)
                    .text(e)
                );
            });
        }
    }
});

$(document).ready(function() {

    tables.forEach( function(e){
        $("#source").append( $("<option></option>")
            .attr("value",e)
            .text(e)
        );
    });
    $("#source").val('master');
});

</script>

<?php $this->append() ?>
