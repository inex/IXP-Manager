<div class="col-sm-12">
    <br>
    <table class="table <?php if( !count( $t->notesInfo[ "custNotes" ] ) ): ?>collapse <?php endif; ?>" id="co-notes-table">
        <thead>
            <tr>
                <th>Title</th>
                <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>
                    <th>Visibility</th>
                <?php endif; ?>
                <th>Updated</th>
                <th>
                    Action
                    <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>
                        &nbsp;<div class="btn-group btn-group-sm">
                            <button id="co-notes-add-btn" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i></button>
                            <button id="co-cust-notify-<?= $t->c->getId() ?>"  class="btn btn-default <?php if( isset( $co_notify_all ) && $co_notify_all ): ?>active<?php endif; ?>"><i class="glyphicon glyphicon-eye-open"></i></button>
                        </div>
                    <?php endif; ?>
                </th>
            </tr>
        </thead>
        <tbody id="co-notes-table-tbody">
            <?php foreach( $t->notesInfo[ "custNotes" ] as $n ): ?>
                <?php if( Auth::getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER || ! $n->getPrivate() ): ?>
                    <tr id="co-notes-table-row-<?= $n->getId() ?>">
                        <td id="co-notes-table-row-title-<?= $n->getId() ?>">
                            <?php if( ( !$t->notesInfo[ "notesLastRead" ] || $n->getUpdated()->format( 'U' ) > $t->notesInfo[ "notesLastRead" ] ) && ( ! $t->notesInfo[ "notesReadUpto" ] || $n->getUpdated()->format( 'U' ) >  $t->notesInfo[ "notesReadUpto" ]  ) ): ?>
                                <span class="label label-success">
                                    <?php if( $n->getUpdated() == $n->getCreated() ): ?>
                                        NEW
                                    <?php else: ?>
                                        UPDATED
                                    <?php endif; ?>
                                 </span>
                            <?php endif; ?>
                            <?= $n->getTitle() ?>
                        </td>

                        <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>
                            <td id="co-notes-table-row-public-<?= $n->getId() ?>">
                                <span class="label label-<?php if( !$n->getPrivate() ): ?>success">PUBLIC<?php else: ?>danger">PRIVATE<?php endif; ?></span>
                            </td>
                        <?php endif; ?>
                        <td id="co-notes-table-row-updated-<?= $n->getId() ?>">
                            <?= $n->getUpdated()->format( 'Y-m-d H:i' ) ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>
                                    <button id="co-notes-notify-<?= $n->getId() ?>"  class="btn btn-default <?php if( is_array( $t->coNotify ) && array_key_exists( $n->getId(), $t->coNotify ) && $t->coNotify[ $n->getId() ] ): ?>active<?php endif; ?>"><i class="glyphicon glyphicon-eye-open"></i></button>
                                <?php endif; ?>

                                <button id="co-notes-view-<?= $n->getId() ?>"  class="btn btn-default"><i class="glyphicon glyphicon-zoom-in"></i></button>

                                <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>
                                    <button id="co-notes-edit-<?= $n->getId() ?>"  class="btn btn-default"><i class="glyphicon glyphicon-pencil"></i></button>
                                    <button id="co-notes-trash-<?= $n->getId() ?>" class="btn btn-default"><i class="glyphicon glyphicon-trash"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if( !count( $t->notesInfo[ "custNotes" ] ) ): ?>
        <p id="co-notes-no-notes-msg">
            There are no notes for this customer. <a href="#" id="co-notes-add-link">Add one...</a>
        </p>
    <?php endif; ?>

    <?php if( Auth::getUser()->getPrivs() ==  \Entities\User::AUTH_SUPERUSER ): ?>

        <!-- Modal dialog for notes / state changes -->
        <div class="modal fade" id="co-notes-dialog" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="notes-modal-label"><span id="co-notes-dialog-title-action">Add a</span> Note for <?= $t->c->getName() ?> </h4>
                    </div>
                    <div class="modal-body" id="notes-modal-body">
                        <div class="alert hide" id="co-notes-warning">
                            <strong>Warning!</strong> Your customer will be able to read this note!
                        </div>

                        <form class="bootbox-form" id="co-notes-form">
                            <input type="text" placeholder="Title" class="bootbox-input bootbox-input form-control" name="title" id="co-notes-ftitle" />
                            <br />
                            <textarea rows="6" class="bootbox-input bootbox-input-textarea form-control" name="note" id="co-notes-fnote"></textarea>
                            <br />
                            <label>
                                <input type="checkbox" name="public" id="co-notes-fpublic" class="bootbox-input bootbox-input-checkbox" value="makePublic" />
                                Make note visible to customer
                            </label>
                            <input type="hidden" name="custid" value="<?= $t->c->getId() ?>" />
                            <input type="hidden" id="notes-dialog-noteid" name="noteid" value="0" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <span class="pull-left"  id="co-notes-dialog-date"></span>
                        <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                        <button id="co-notes-fadd"  type="button" class="btn btn-primary"                               >Add</button>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="co-notes-view-dialog" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="co-notes-view-dialog-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="bootbox-body" id="co-notes-view-dialog-note"></div>
                </div>
                <div class="modal-footer">
                    <span class="pull-left"  id="co-notes-view-dialog-date"></span>
                    <button id="notes-modal-btn-cancel"  type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> OK</button>
                </div>
            </div>
        </div>
    </div>

</div>
