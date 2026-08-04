<script>

    let category = "<?= $t->ee( $t->category, "js" ) ?>";

    // from phpjs - MIT license:
    function number_format (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        let n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                let k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // Define a custom format function for scale and type
    // This function is used with 'average' and 'max' metric types which are transfer _rates_
    let myScale = function( data, type, full ) {

        if( type === 'sort' || type === 'type' ) {
            return data;
        }

        let strFormat;

        switch( category ) {
            case 'bytes':
                // Note: this table doesn't seem to support displaying data in bytes?
                strFormat = [ "Bytes", "KBytes", "MBytes", "GBytes", "TBytes" ];
                break;
            case 'errs':
            case 'discs':
            case 'pkts':
                strFormat = [ "pps", "Kpps", "Mpps", "Gpps", "Tpps" ];
                break;
            default:
                strFormat = [ "bps", "Kbps", "Mbps", "Gbps", "Tbps" ];
                break;
        }

        let retString = "";

        for( let i = 0; i < strFormat.length; i++ )  {
            if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
                retString =  number_format( data, 0 ) + '&nbsp;' + strFormat[i];
                break;
            } else {
                data = data / 1000;
            }
        }

        return retString;
    };

    // myScaleTotal is used with 'total' metric types which are a quantity, not a rate
    let myScaleTotal = function( data, type, full ) {

        if( type === 'sort' || type === 'type' ) {
            return data;
        }

        let strFormat;

        switch( category ) {
            case 'errs':
            case 'discs':
            case 'pkts':
                strFormat = [ "p", "Kp", "Mp", "Gp", "Tp" ];
                break;
            default:
                // Data in traffic daily table is stored as bits. To convert to total in bytes,
                // divide by 8.
                strFormat = [ "B", "KB", "MB", "GB", "TB" ];
                data /= 8;
                break;
        }

        let retString = "";

        for( let i = 0; i < strFormat.length; i++ )  {
            if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
                retString =  number_format( data, 0 ) + strFormat[i];
                break;
            } else {
                data = data / 1000;
            }
        }

        return retString;
    };

    let scalefn  = <?= $t->metric === 'data' ? 'myScaleTotal' : 'myScale' ?>;
    let tableList = $( '#ixpDataTable' );

    tableList.dataTable({
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,

        "aLengthMenu": [[20, 50, 100, 500, -1], [20, 50, 100, 500, "All"]],

        "bAutoWidth": false,

        "aaSorting": [[6, 'desc']],
        "iDisplayLength": 100,
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]},
            {"render": scalefn, "aTargets": [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]}
        ]
    });


    $(document).ready(function() {
        tableList.show();
    });

</script>