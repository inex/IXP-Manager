<!-- Modal dialog for views mac address with different formats -->
<div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="notes-modal-label">MAC Address</h4>
            </div>
            <div class="modal-body" id="notes-modal-body">
                <div class="input-group">
                    <input class="form-control mac-input lowerCase" readonly id="mac">
                    <div class="input-group-btn">
                        <button id="btn-copy-mac" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#mac">
                            <span class="glyphicon glyphicon-copy"></span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <input class="form-control mac-input lowerCase" readonly id="macComma">
                    <div class="input-group-btn">
                        <button id="btn-copy-mac-comma" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#macComma">
                            <span class="glyphicon glyphicon-copy"></span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <input class="form-control mac-input lowerCase" readonly id="macDot">
                    <div class="input-group-btn">
                        <button id="btn-copy-mac-dot" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#macDot">
                            <span class="glyphicon glyphicon-copy"></span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <input class="form-control mac-input lowerCase" readonly id="macDash">
                    <div class="input-group-btn">
                        <button id="btn-copy-mac-dash" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#macDash">
                            <span class="glyphicon glyphicon-copy"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="notes-modal-btn-case"  type="button" class="btn btn-success"><i class="glyphicon glyphicon-text-size"></i> UpperCase</button>
                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>