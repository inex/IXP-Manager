<div>
    <h4>
        Metadata for <?= $t->ee( $t->file->name ) ?>
    </h4>

    <table class="tw-mt-8">
        <tr class="tw-border-t-1 tw-border-blue-500">
            <td class="tw-text-right tw-font-bold tw-p-2">
                Created By
            </td>
            <td class="tw-pl-4 tw-font-mono tw-text-sm tw-p-2">
                <?php if( $t->created_by ): ?>
                    <?= $t->ee( $t->created_by->username ) ?> (<?= $t->ee( $t->created_by->name ) ?>)
                <?php else: ?>
                    <em>User no longer exists in database.</em>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="tw-border-t-1 tw-border-blue-500">
            <td class="tw-text-right tw-font-bold tw-p-2">
                Path
            </td>
            <td class="tw-pl-4 tw-font-mono tw-text-sm tw-p-2">
                <?= $t->dspath ?>
            </td>
        </tr>
        <tr class="tw-border-t-1 tw-border-blue-500">
            <td class="tw-text-right tw-font-bold tw-p-2">
                Created
            </td>
            <td class="tw-pl-4 tw-font-mono tw-text-sm tw-p-2">
                <?= $t->created_at ?>
            </td>
        </tr>
        <tr class="tw-border-t-1 tw-border-blue-500">
            <td class="tw-text-right tw-font-bold tw-p-2">
                Last Modified
            </td>
            <td class="tw-pl-4 tw-font-mono tw-text-sm tw-p-2">
                <?= date( 'Y-m-d H:i:s', $t->last_modified ) ?>
            </td>
        </tr>
        <tr class="tw-border-t-1 tw-border-b-1 tw-border-blue-500">
            <td class="tw-text-right tw-font-bold tw-p-2">
                Size
            </td>
            <td class="tw-pl-4 tw-font-mono tw-text-sm tw-p-2">
                <?= $t->scaleFilesize( $t->size ) ?>
            </td>
        </tr>
    </table>
</div>