<!-- Modal dialog for peering manager -->
<div class="modal fade" id="modal-peering-request" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-peering-request-content">

            <div class="modal-header">
                <h4 class="modal-title" id="peering-modal-label"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= __( 'Close' ) ?>"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body" id="peering-modal-body">
            </div>
            <div class="modal-footer" id="modal-peering-request-footer">
                <button id="modal-peering-request-close"  type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= __( 'Cancel' ) ?>
                </button>
                <button class="btn btn-primary btn-footer-modal btn-footer-modal-email collapse" id="modal-peering-request-marksent" data-toggle="tooltip" title="<?= __( 'Don\'t send this email but mark it as sent - useful if you are sending requests manually but want to track them here.' ) ?>" >
                    <?= __( 'Mark Sent' ) ?>
                </button>
                <button class="btn btn-primary btn-footer-modal btn-footer-modal-email collapse" id="modal-peering-request-sendtome" data-toggle="tooltip" title="<?= __( 'Just send this email to me so I can see how it looks.' ) ?>" >
                    <?= __( 'Send to Me' ) ?>
                </button>
                <button class="btn btn-success btn-footer-modal btn-footer-modal-email collapse" id="modal-peering-request-send" >
                    <?= __( 'Send' ) ?>
                </button>
                <button class="btn btn-success btn-footer-modal btn-footer-modal-note collapse" id="modal-peering-notes-save" >
                    <?= __( 'Save' ) ?>
                </button>

            </div>
        </div>
    </div>
</div>