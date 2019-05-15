
<div class="card mt-4" id="core-link-<?= $t->nbLink ?>">
    <div class="card-header d-flex">
        <div class="mr-auto">
            <h4>
                Link <?= $t->nbLink ?>:
            </h4>
        </div>
        <?php if( $t->nbLink > 1 ): ?>
            <div clas="my-auto">
                <button title="Remove link" id="remove-core-link-<?= $t->nbLink ?>" class="btn btn-sm btn-white"><i class="fa fa-trash"></i></button>
            </div>
        <?php endif; ?>
    </div>


    <div class="card-body row">

        <div class="col-sm-12">
            <div id="message-<?= $t->nbLink ?>"></div>

            <div class="form-group row">
                <label for="sp-a-1" class="control-label col-sm-6 col-lg-3"> Side A Switch Port</label>
                <div class="col-lg-4 col-sm-6">
                    <select class="chzn-select form-control" id="<?= 'sp-a-'.$t->nbLink ?>" name="<?= 'sp-a-'.$t->nbLink ?>">
                    </select>
                </div>
            </div>

            <input id="<?= 'hidden-sp-a-'.$t->nbLink ?>" type="hidden" name="<?= 'hidden-sp-a-'.$t->nbLink ?>" value="null">

            <div class="form-group row">
                <label for="sp-a-1" class="control-label col-sm-6 col-lg-3"> Side B Switch Port</label>
                <div class="col-lg-4 col-sm-6">
                    <select class="chzn-select form-control" id="<?= 'sp-b-'.$t->nbLink ?>" name="<?= 'sp-b-'.$t->nbLink ?>">
                    </select>
                </div>
            </div>

            <input id="<?= 'hidden-sp-b-'.$t->nbLink ?>" type="hidden" name="<?= 'hidden-sp-b-'.$t->nbLink ?>" value="null">

            <div class="form-group row">
                <label for="<?= 'enabled-cl-'.$t->nbLink ?>" class="control-label col-sm-6 col-lg-3">Enabled</label>
                <div class="col-lg-4 col-sm-6">
                    <input type="hidden" name="<?= 'enabled-cl-'.$t->nbLink ?>" value="0">
                    <input id="<?= 'enabled-cl-'.$t->nbLink ?>" type="checkbox" name="<?= 'enabled-cl-'.$t->nbLink ?>" checked="checked" value="1">
                </div>
            </div>


            <?php if( $t->bundleType == \Entities\CoreBundle::TYPE_ECMP ): ?>

                <div class="form-group row">
                    <label for="<?= 'bfd-'.$t->nbLink ?>" class="control-label col-sm-6 col-lg-3">BFD</label>
                    <div class="col-lg-4 col-sm-6">
                        <input type="hidden" name="<?= 'bfd-'.$t->nbLink ?>" value="0">
                        <input id="<?= 'bfd-'.$t->nbLink ?>" type="checkbox" name="<?= 'bfd-'.$t->nbLink ?>" value="1">
                    </div>
                </div>

            <?php endif; ?>

            <?php if( $t->bundleType == \Entities\CoreBundle::TYPE_ECMP ): ?>


                <div class="form-group row">
                    <label for="<?= 'subnet-'.$t->nbLink ?>" class="control-label col-sm-6 col-lg-3">Subnet</label>
                    <div class="col-lg-4 col-sm-6">
                        <input class="form-control" placeholder="192.0.2.0/30" id="<?= 'subnet-'.$t->nbLink ?>" type="text" name="<?= 'subnet-'.$t->nbLink ?>">
                    </div>
                </div>

            <?php endif; ?>

    </div>

</div>

<?php $this->section( 'scripts' ) ?>
<script>
    $(document).ready( function() {
        dropdownChosen();
        event();
    });

    /**
     * initialize the library 'chosen' on the dropdowns
     */
    function dropdownChosen(){
        $( "#s-a-"+<?= $t->nbLink ?> ).select2();
        $( "#sp-a-"+<?= $t->nbLink ?> ).select2();
        $( "#s-b-"+<?= $t->nbLink ?> ).select2();
        $( "#sp-b-"+<?= $t->nbLink ?> ).select2();
    }

    function event(){
        /**
         * on click even delete the core link
         */
        $( document ).on( 'click', "button[id|='remove-core-link']" ,function(e){
            e.preventDefault();
            let id = ( this.id ).substring( 17 );
            let beforeId = id - 1;

            $( "#core-link-" + id ).remove();

            // allow the click on the delete button on the previous core link
            $( "#remove-core-link-"+beforeId ).prop( 'disabled', false );

            $("#nb-core-links").val( beforeId );
            disableDropDown( id - 1 , false );
        });
    }
</script>
<?php $this->append() ?>