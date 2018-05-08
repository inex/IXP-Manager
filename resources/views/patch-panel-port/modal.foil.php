<!-- Modal dialog for notes / state changes -->
<div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="notes-modal-label">Notes</h4>
            </div>
            <div class="modal-body" id="notes-modal-body">
                <p id="notes-modal-body-intro">
                    Consider adding details to the notes such as a internal ticket reference to the cease request / whom you have been dealing with / expected cease date / etc..
                    <br><br>
                </p>

                <h4>Public Notes</h4>

                <div class="">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active">
                            <a class="tab-link-body-note" href="#body1">Notes</a>
                        </li>
                        <li role="presentation">
                            <a class="tab-link-preview-note" href="#preview1">Preview</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="body1">

                            <textarea id="notes-modal-body-public-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Public Notes"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview1">
                            <div class="well well-preview" style="background: rgb(255,255,255);">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <br>

                <h4>Private Notes</h4>
                <div class="">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active">
                            <a class="tab-link-body-note" href="#body2">Notes</a>
                        </li>
                        <li role="presentation">
                            <a class="tab-link-preview-note" href="#preview2">Preview</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="body2">

                            <textarea id="notes-modal-body-private-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Private Notes"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview2">
                            <div id="well-preview" class="well well-preview" style="background: rgb(255,255,255);">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>



                <div id="notes-modal-body-div-pi-status">
                    <br><br>
                    <span>Update Physical Port State To: </span>
                    <select title="Physical Interface States" id="notes-modal-body-pi-status">
                        <option value="0"></option>
                        <?php foreach( Entities\PhysicalInterface::$STATES as $i => $s ): ?>
                            <option value="<?= $i ?>"><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <input  id="notes-modal-ppp-id"      type="hidden" name="notes-modal-ppp-id" value="">
                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button id="notes-modal-btn-confirm" type="button" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
            </div>
        </div>
    </div>
</div>