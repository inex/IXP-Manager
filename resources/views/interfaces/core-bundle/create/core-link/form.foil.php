<div class="card mt-4 collapse" id="core-link-example">
    <div class="card-header d-flex">
        <div class="mr-auto">
            <h4 class="title-new-cl">
            </h4>
        </div>

        <div class="my-auto">
            <button title="Remove link" id="delete-core-bundle" class="btn btn-sm btn-white delete-core-link"><i class="fa fa-trash"></i></button>
        </div>
    </div>

    <div class="card-body row">
        <div class="col-sm-12">
            <div id="" class="message-new-cl message"></div>
            <div class="form-group row">
                <label for="sp-a-1" class="control-label col-sm-6 col-lg-3">Side A Switch Port</label>
                <div class="col-lg-4 col-sm-6">
                    <select class="form-control sp-dd cl-input sp-a" id="" data-value="sp-a" data-value-side="a" name="">
                    </select>
                    <small class="form-text text-muted former-help-text">The switch port for the 'a side' of the core link.</small>
                </div>
            </div>

            <input id="" data-value="hidden-sp-a" class="cl-input hidden-sp-a" type="hidden" name="" value="null">

            <div class="form-group row">
                <label for="sp-a-1" class="control-label col-sm-6 col-lg-3">Side B Switch Port</label>
                <div class="col-lg-4 col-sm-6">
                    <select class="form-control sp-dd cl-input sp-b" id="" data-value="sp-b" data-value-side="b" name="">
                    </select>
                    <small class="form-text text-muted former-help-text">The switch port for the 'b side' of the core link.</small>
                </div>
            </div>

            <input id="" data-value="hidden-sp-b" class="cl-input hidden-sp-b" type="hidden" name="" value="null">

            <div class="form-group row">
                <label class="control-label col-sm-6 col-lg-3">Enabled</label>
                <div class="col-lg-4 col-sm-6">
                    <input type="hidden" name="" data-value="enabled-cl" class="cl-input checkbox-cl-hidden" value="0">
                    <input id="" data-value="enabled-cl" class="cl-input enabled-cl" type="checkbox" name="" checked="checked" value="1">
                    <small class="form-text text-muted former-help-text">If the core link is enabled. Affects graphing but otherwise informational unless you are provisioning your switches from IXP Manager.</small>
                </div>
            </div>

            <div class="type-ecmp-only" >
                <div class="form-group row">
                    <label class="control-label col-sm-6 col-lg-3">BFD</label>
                    <div class="col-lg-4 col-sm-6">
                        <input type="hidden" data-value="bfd" class="cl-input checkbox-cl-hidden" name="" value="0">
                        <input id="" data-value="bfd" type="checkbox" class="cl-input bfd" name="" value="1">

                        <small class="form-text text-muted former-help-text">If the BFD protocol should be configured across the links of this bundle. Informational unless you are provisioning your switches from IXP Manager.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="control-label col-sm-6 col-lg-3">Subnet</label>
                    <div class="col-lg-4 col-sm-6">
                        <input class="form-control cl-input subnet" placeholder="192.0.2.0/31" id="" data-value="subnet" type="text" name="">
                        <small class="form-text text-muted former-help-text">The subnet to use across the core link. Informational unless you are provisioning your switches from IXP Manager.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>