<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
IXP Manager Settings
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/latest/features/settings/">
            Documentation
        </a>
    </div>
<?php $this->append() ?>



<?php $this->section('content') ?>
<div class="row">
    <div class="col-12">

        <?= $t->alerts() ?>

        <?php if( count( $t->errors ) ): ?>
            <div class="tw-bg-red-100 tw-border-l-4 tw-border-red-500 tw-text-red-700 p-4 alert-dismissible mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center"><i class="fa fa-exclamation-triangle fa-2x "></i></div>
                    <div class="col-sm-12">
                        There were validation errors with your settings. Please correct them and try again.<br><br>

                        <ul>
                        <?php foreach( $t->errors->all() as $error ): ?>
                            <li> <?= $error ?> </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card col-sm-12">
            <div class="card-body">

                <?=
                    Former::open()->method('POST')
                        ->id('envForm')
                        ->action(route('settings@update'))
                        ->customInputWidthClass( 'col-8' )
                        ->customLabelWidthClass( 'col-4' )
                        ->actionButtonsCustomClass( "grey-box")
                        ->rules($t->rules);
                ?>

                <ul class="tabNavMenu" id="envFormTabs">

                    <?php
                        $first = true;
                        foreach( $t->settings["panels"] as $panel => $content ): ?>

                        <li>
                            <button class="tabButton <?php if( $first ) { echo 'active'; $first = false; } ?>"
                                id="<?= $panel ?>-tab" data-target="#<?= $panel ?>-content" type="button"
                            ><?= $content["title"] ?></button>
                        </li>

                    <?php endforeach; ?>

                </ul>

                <div class="tabContent" id="envFormTabContents">

                    <?php $first = true;
                        foreach( $t->settings["panels"] as $panel => $content ) { ?>

                        <div class="tabPanel <?php if( $first ) { echo 'active'; $first = false; } ?>" id="<?= $panel ?>-content" role="tabpanel" aria-labelledby="<?= $panel ?>-content">

                            <?php if( isset( $content["description"] ) && $content["description"] !== "" ): ?>

                                <div class="alert alert-info" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div class="text-center">
                                            <i class="fa fa-question-circle fa-2x"></i>
                                        </div>
                                        <div class="col-sm-12"><?= $content["description"] ?></div>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <?php foreach( $content["fields"] as $field => $param ) {

                                $label = $param["name"];

                                if( isset( $param["docs_url"] ) && $param["docs_url"] ) {
                                    $label .= ' <a href="' . $param["docs_url"] . '" target="_blank"><i class="ml-2 fa fa-external-link"></i></a>';
                                }

                                // value comes from config, not .env, as config() includes defaults and anything set in .env
                                $value = config( $param['config_key'] );

                                echo "<div class=\"inputWrapper\">\n";

                                    switch( $param["type"] ) {

                                        case 'radio':
                                            if( isset( $param["invert"] ) && $param["invert"] ) {
                                                $value = !$value;
                                            }
                                            echo Former::checkbox($field)->label($label)->check( (bool)$value );
                                            break;

                                        case 'select':
                                            if( $param["options"]["type"] === 'array' ) {
                                                $options = $param["options"]["list"];
                                            } else if( $param["options"]["type"] === 'countries' ) {
                                                $options = $t->getCountriesSelection();
                                            } else {
                                                $options = $this->getSelectOptions( $param["options"]["list"]['model'], $param["options"]["list"]['key'], $param["options"]["list"]["value"] );
                                            }

                                            echo Former::select($field)->label($label)->options($options,$value)
                                                    ->placeholder('Select an option')
                                                    ->addClass( 'chzn-select' );
                                            break;

                                        case 'textarea':
                                            echo Former::textarea($field)->label($label)->value($value);
                                            break;

                                        default: // text
                                            echo Former::text($field)->label($label)->value($value);


                                    } // switch type

                                    if( isset( $param["help"] ) && $param["help"] !== ''): ?>
                                        <div class="small"><i class="fa fa-info-circle tw-text-blue-600"></i> <?= $param["help"] ?></div>
                                    <?php endif;

                                echo "</div>\n";
                            } // foreach field
                            ?>

                        </div>

                        <?php } // foreach panel ?>

                    </div>

                    <?=
                        Former::actions(
                            Former::primary_submit( 'Save Changes' )->id('updateButton')->class( "mb-2 mb-sm-0" )
                        )
                   ?>

                   <?= Former::close() ?>

            </div>
        </div>

    </div>
</div>
<?php $this->append() ?>
