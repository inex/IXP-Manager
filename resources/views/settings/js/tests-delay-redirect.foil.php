<script>
    // Saving writes .env, which makes `artisan serve` restart. A normal submit follows the
    // redirect while the server is down, making Dusk tests flaky. Under test, POST via fetch
    // without following the redirect, give the server time to come back, then reload.
    document.getElementById( 'envForm' ).addEventListener( 'submit', async function( e ) {
        e.preventDefault();
        document.getElementById( 'updateButton' ).disabled = true;
        // drop stale alerts so tests waiting on alert text can't match the previous page
        document.querySelectorAll( '[role="alert"]' ).forEach( el => el.remove() );

        try {
            await fetch( this.action, { method: 'POST', body: new FormData( this ), redirect: 'manual', credentials: 'same-origin' } );
        } catch( err ) {
            // connection may drop if the server restarts mid-response; the save has happened
        }

        setTimeout( () => window.location.href = <?= json_encode( route( 'settings@index' ) ) ?>, 2000 );
    });
</script>