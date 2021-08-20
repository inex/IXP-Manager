<script>
  <?php if( $t->protocol === 6 ): ?>
      let cb_decimal  = $('#decimal' );
      let cb_overflow = $('#div-overflow');

      cb_decimal.on( 'change', function() {
          if( cb_decimal.prop('checked') ) {
              cb_overflow.show();
          } else {
              cb_overflow.hide();
          }
      }).trigger( 'change' );
  <?php endif; ?>
</script>