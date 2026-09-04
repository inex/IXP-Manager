<script type="module">
    $(document).ready( function() {
        const duplicate = $("#duplicate").val() === "1";
        const dd_ipv6   = $( "#ipv6address" );
        const dd_ipv4   = $( "#ipv4address" );

        // If we are duplicating then we need to select / set the IP addresses
        // BUT ONLY AFTER ALL THE AJAX HAS RUN
        if( duplicate ) {
            const requests = window.ajaxRequests || [];
            $.when( $, ...requests ).then( function() {
                const ipv6 = $( "#original-ipv6address" ).val();
                const ipv4 = $( "#original-ipv4address" ).val();

                // do these exist in the dropdown?
                if( ipv6 && !dd_ipv6.find( `option[value='${ipv6}']` ).length ) {
                    let newOption = new Option( ipv6, ipv6, true, false);
                    dd_ipv6.append(newOption);
                }

                if( ipv6 ) {
                    dd_ipv6.val( ipv6 ).trigger('change.select2');
                }

                // do these exist in the dropdown?
                if( ipv4 && !dd_ipv4.find( `option[value='${ipv4}']` ).length ) {
                    let newOption = new Option( ipv4, ipv4, true, false);
                    dd_ipv4.append(newOption);
                }

                if( ipv4 ) {
                    dd_ipv4.val( ipv4 ).trigger('change.select2');
                }
            });
        }
    });
</script>
