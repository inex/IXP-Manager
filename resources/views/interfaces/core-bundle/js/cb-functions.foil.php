<script type="module">
    /**
     * check if the subnet is valid and display a message
     */
    function checkSubnet( subnet ) {
        const $subnet = $(subnet);
        const value = $subnet.val().trim();

        // 1. Reset state completely
        $subnet.removeClass('is-valid is-invalid');
        $subnet.parent().find('.invalid-feedback, .valid-feedback').remove();

        if (value !== '') {
            if (!validSubnet(value)) {
                $subnet.addClass('is-invalid');
                $subnet.parent().append(
                    `<span class='help-block invalid-feedback' style='display: block'>The subnet is not valid</span>`
                );
            } else {
                $subnet.addClass('is-valid');
                $subnet.parent().append(
                    `<span class='help-block valid-feedback' style='display: block'>The subnet is valid</span>`
                );
            }
        }
    }

    /**
     * Check if the subnet provided is valid
     */
    function validSubnet( subnet ){
        if (!subnet.includes('/')) return false;
        return Address4.isValid(subnet);
    }

    // Expose helpers globally for core bundle wizard scripts
    window.checkSubnet           = checkSubnet;
    window.validSubnet           = validSubnet;
</script>