<?php
    /** @var Foil\Template\Template $t */

    // the locales this instance offers - if there's fewer than two there is
    // nothing for the user to choose between and we show nothing:
    $locales = config( 'ixp_fe.locales', [] );

    // what the user gets if they express no preference of their own:
    $default = Auth::getUser()->customer?->locale() ?? config( 'app.locale' );

    // '' lets the user say "no preference of my own":
    $options = [ '' => __( 'Use the default (:language)', [ 'language' => $locales[ $default ] ?? $default ] ) ] + $locales;
?>

<?php if( count( $locales ) > 1 ): ?>
    <div class="col-lg-6 col-md-12">
        <h3>
            <?= __( 'Language' ) ?>
        </h3>
        <hr>
        <?= Former::open()
            ->populate( $t->language )
            ->method( 'post' )
            ->id( 'language' )
            ->action( route( 'profile@update-language' ) )
            ->customInputWidthClass( 'col-sm-10' )
            ->actionButtonsCustomClass( 'grey-box' );
        ?>

        <?= Former::select( 'locale' )
            ->label( __( 'Language' ) )
            ->options( $options )
            ->blockHelp( __( 'The language used for your view of this site. Not all text is translated and anything that is not will be shown in English.' ) );
        ?>

        <?= Former::actions(
            Former::primary_submit( __( 'Set Language' ) )
        );
        ?>

        <?= Former::close() ?>
    </div>
<?php endif; ?>
