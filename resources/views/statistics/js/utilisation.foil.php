<script>

    let category = "<?= $t->category ?>";

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

    //Define a custom format function for scale and type
    let myScale = function( data, type, full ) {
        if( type === 'sort' || type === 'type' ) {
            return data;
        }

        let strFormat;

        switch( category ) {
            case 'bytes':
                strFormat = [ "Bytes", "KBytes", "MBytes", "GBytes", "TBytes" ];
                // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
                // data = data / 8.0;
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
                retString =  number_format( data, 1 ) + '&nbsp;' + strFormat[i];
                break;
            } else {
                data = data / 1000;
            }
        }

        return retString;
    };

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
                strFormat = [ "B", "KB", "MB", "GB", "TB" ];
                // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
                // oData /= 8;
                break;
        }

        let retString = "";

        for( let i = 0; i < strFormat.length; i++ )  {
            if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
                retString =  number_format( data, 1 ) + strFormat[i];
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
            {"render": scalefn, "aTargets": [3, 4, 5]}
        ]
    });

    $(document).ready(function() {
        tableList.show();
    });

</script>