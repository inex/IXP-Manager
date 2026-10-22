<!-- Modal dialog for notes / state changes -->
<div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="notes-modal-label">Notes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="notes-modal-body">
                <p id="notes-modal-body-intro" class="tw:mb-2" >
                    Consider adding details to the notes such as a ticket reference to the cease request / whom you have been dealing with / expected cease date / etc.
                </p>
                <hr />

                <div id="notes-modal-body-div-pi-status" class="tw:mb-2">
                    <label for="notes-modal-body-pi-status" class="col-md-6">Update Physical Port State To:</label>
                    <div class="col-md-6">
                        <select title="Physical Interface States" id="notes-modal-body-pi-status" class="chzn-select">
                            <?php foreach( \IXP\Models\PhysicalInterface::$STATES as $i => $s ): ?>
                                <option value="<?= $i ?>"><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="notes-modal-body-div-colo-ref" class="tw:mb-2">
                    <label for="notes-modal-body-colo-ref" class="col-md-6">Colocation Reference:</label>
                    <div class="col-md-6">
                        <input type="text" id="notes-modal-body-colo-ref" class="form-control">
                    </div>
                </div>

                <div id="notes-modal-body-div-unpatch" class="tw:mb-2">
                    <label id="notes-modal-body-unpatch-label" for="notes-modal-body-unpatch" class="col-md-8">Unpatch Port:</label>
                    <input type="checkbox" id="notes-modal-body-unpatch" class="tw:ml-2">
                </div>

                <div id="notes-modal-body-div-set-prewired" class="tw:mb-2">
                    <label for="notes-modal-body-set-prewired" class="col-md-8">Set to Prewired:</label>
                    <input type="checkbox" id="notes-modal-body-set-prewired" class="tw:ml-2">
                </div>

                <div id="notes-modal-body-div-keep-duplex" class="tw:mb-2">
                    <label for="notes-modal-body-keep-duplex" class="col-md-8">Keep as duplex pair:</label>
                    <input type="checkbox" id="notes-modal-body-keep-duplex" class="tw:ml-2" checked>
                </div>

                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#public-note-tab">Public Notes</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-preview-note nav-link" href="#private-note-tab">Private Notes</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-body tw:pt-0">
                        <div role="tabpanel" class="tab-pane show active" id="public-note-tab">
                            <div class="card mt-4 mb-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs">
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-body-note nav-link active" href="#body1">Notes</a>
                                        </li>
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-preview-note nav-link" href="#preview1">Preview</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content card-body">
                                    <div role="tabpanel" class="tab-pane show active" id="body1">
                                        <textarea id="notes-modal-body-public-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Public Notes"></textarea>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="preview1">
                                        <div class="bg-light p-4 well-preview">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="private-note-tab">
                            <div class="card mt-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs">
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-body-note nav-link active" href="#body2">Notes</a>
                                        </li>
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-preview-note nav-link" href="#preview2">Preview</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content card-body">
                                    <div role="tabpanel" class="tab-pane show active" id="body2">
                                        <textarea id="notes-modal-body-private-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Private Notes"></textarea>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="preview2">
                                        <div class="bg-light p-4 well-preview">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input  id="notes-modal-ppp-id"      type="hidden" name="notes-modal-ppp-id" value="">
                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button id="notes-modal-btn-confirm" type="button" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
            </div>
        </div>
    </div>
</div>