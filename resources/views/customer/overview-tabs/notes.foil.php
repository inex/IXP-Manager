<table class="w-100 table table-striped table-note <?php if( !$t->notes->count() ): ?>collapse <?php endif; ?>" id="co-notes-table">
    <thead class="thead-dark">
        <tr>
            <th>
                Title
            </th>
            <?php if( $t->isSuperUser ): ?>
                <th>
                    Visibility
                </th>
            <?php endif; ?>
            <th>
                Updated
            </th>
            <th>
                Action
                <?php if( $t->isSuperUser ): ?>
                    &nbsp;<div class="btn-group btn-group-sm ml-2">
                        <a id="btn-create-note" href="<?= route( 'customer-notes@create', [ 'cust' => $t->c->id ]  ) ?>" class="btn btn-white btn-create-note">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a id="co-cust-notify-<?= $t->c->id ?>" href="<?= route( 'customer-notes@notify-toggle-customer', [ 'cust' => $t->c->id ]  ) ?>" class="btn btn-white btn-watch">
                            <?= isset( $t->user->prefs[ 'notes' ][ 'customer_watching' ][ $t->c->id ] ) ? 'Unwatch All' : 'Watch All' ?>
                        </a>

                        <a class="btn btn-white" href="https://docs.ixpmanager.org/usage/customer-notes/" target="_blank">
                            Help
                        </a>
                    </div>
                <?php endif; ?>
            </th>
        </tr>
    </thead>

    <tbody id="co-notes-table-tbody">
        <?php
            /** @var \IXP\Models\CustomerNote $n */
        foreach( $t->notes as $n ):?>
            <?php if( $t->isSuperUser || !$n->private ): ?>
                <tr id="co-notes-table-row-<?= $n->id ?>">
                    <td id="co-notes-table-row-title-<?= $n->id ?>">
                        <?php if( ( !$t->notesInfo[ "notesLastRead" ] || $n->updated_at > $t->notesInfo[ "notesLastRead" ] ) && ( !$t->notesInfo[ "notesReadUpto" ] || $n->updated_at >  $t->notesInfo[ "notesReadUpto" ]  ) ): ?>
                            <?php if( $n->updated_at->eq( $n->created_at ) ): ?>
                                <span class="badge badge-primary"> NEW </span>
                            <?php else: ?>
                                <span class="badge badge-success"> UPDATED </span>
                            <?php endif; ?>
                            &nbsp;&nbsp;
                        <?php endif; ?>
                        <?= $t->ee( $n->title ) ?>
                    </td>

                    <?php if( $t->isSuperUser ): ?>
                        <td id="co-notes-table-row-public-<?= $n->id ?>">
                            <?php if( !$n->private ): ?>
                                <span class="badge badge-success">
                                    PUBLIC
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    PRIVATE
                                </span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                    <td id="co-notes-table-row-updated-<?= $n->id ?>">
                        <?= $n->updated_at ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <?php if( $t->isSuperUser ): ?>
                                <a href="<?= route( 'customer-notes@notify-toggle-note', [ 'cn' => $n->id ] ) ?>"  class="btn btn-white btn-watch">
                                    <?= isset( $t->user->prefs[ 'notes' ][ 'note_watching' ][ $n->id ] ) ? 'Unwatch' : 'Watch' ?>
                                </a>
                            <?php endif; ?>

                            <a href="<?= route( 'customer-notes@get', [ 'cn' => $n->id ] ) ?>" class="btn btn-white btn-view-note">
                                <i class="fa fa-eye"></i>
                            </a>

                            <?php if( $t->isSuperUser ): ?>
                                <a href="<?= route( 'customer-notes@update', [ 'cn' => $n->id ]  ) ?>" data-object-id="<?= $n->id ?>" class="btn btn-white btn-edit-note">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="<?= route( 'customer-notes@delete', [ 'cn' => $n->id ]  ) ?>" class="btn btn-white btn-delete-note">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if( !$t->notes->count() ): ?>
    <p class="mt-4" id="co-notes-no-notes-msg">
        There are no notes for this customer.
        <a class="btn btn-white ml-2" href="#" id="co-notes-add-link">Create one...</a>
    </p>
<?php endif; ?>

<?php if( $t->isSuperUser ): ?>
    <!-- Modal dialog for notes / state changes -->
    <div class="modal fade" id="co-notes-dialog" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="notes-modal-label">
                        <span id="co-notes-dialog-title-action">
                          Create a
                        </span>
                        Note for <?= $t->c->name ?>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body" id="notes-modal-body">
                    <div class="alert alert-warning" id="co-notes-warning" style="display: none;">
                        <strong>Warning!</strong> Your customer will be able to read this note!
                    </div>
                    <form class="bootbox-form" id="co-notes-form">
                        <input type="text" placeholder="Title" class="bootbox-input bootbox-input form-control" name="title" id="co-notes-ftitle" />
                        <div class="card mt-4">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li role="presentation" class="nav-item" >
                                        <a class="tab-link-body-note nav-link active" href="#body" data-toggle="tab">Notes</a>
                                    </li>
                                    <li role="presentation" class="nav-item">
                                        <a class="tab-link-preview-note nav-link" href="#preview" data-toggle="tab">Preview</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content card-body">
                                <div role="tabpanel" class="tab-pane show active" id="body">
                                    <textarea rows="6" class="bootbox-input bootbox-input-textarea form-control" name="note" id="co-notes-fnote"></textarea>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="preview">
                                    <div id="co-notes-fpreview" class="bg-light shadow-sm well-preview p-4">
                                        Loading...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label class="mt-4">
                            <input type="checkbox" name="public" id="co-notes-fpublic" class="bootbox-input bootbox-input-checkbox" value="makePublic" />
                            Make note visible to customer
                        </label>
                        <p>
                            <em>Markdown formatting supported (and encouraged!)</em>
                        </p>
                        <input type="hidden" id="co-note-dialog-action" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <span class="mr-auto"  id="co-notes-dialog-date"></span>
                    <button id="notes-modal-btn-cancel"  type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button id="co-notes-fadd"  type="button" class="btn btn-primary">
                        Create
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="co-notes-view-dialog" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="co-notes-view-dialog-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="bootbox-body" id="co-notes-view-dialog-note"></div>
            </div>
            <div class="modal-footer">
                <span class="mr-auto"  id="co-notes-view-dialog-date"></span>
                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>