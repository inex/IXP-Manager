<?php
  $c = $t->c; /** @var $c \IXP\Models\Customer */
?>

<table class="table table-striped table-responsive-ixp-action collapse w-100">
    <thead class="thead-dark">
        <tr>
            <th>
                Name
            </th>
            <th>
                Email
            </th>
            <th>
                Phone
            </th>
            <th>
                Role(s)
            </th>
            <th>
                Actions
                <a id="contacts-add-btn" class="btn btn-white btn-sm ml-2" href="<?= route( 'contact@create', [ 'cust' => $c->id ] ) ?>">
                    <i class="fa fa-plus"></i>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $c->contacts as $co ): ?>
            <tr>
                <td>
                    <?= $t->ee( $co->name ) ?>
                </td>
                <td>
                    <?= $t->ee( $co->email ) ?>
                </td>
                <td>
                    <?= $t->ee( $co->phone ) ?>
                    <?php if( $co->phone && $co->mobile ): ?>
                        /
                    <?php endif; ?>
                    <?=  $t->ee( $co->mobile ) ?>
                </td>
                <td>
                    <?php foreach( $co->contactRoles as $group ): ?>
                        <span class="badge badge-info">
                            <?= $t->ee( $group->name ) ?>
                        </span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-white" id="cont-list-edit-<?= $co->id ?>"href="<?= route( "contact@edit", [ "id" => $co->id ] ) ?>">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-white btn-delete" id="btn-delete-<?= $co->id ?>" href="<?= route( 'contact@delete' , [ 'id' => $co->id ]  )  ?>" title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>