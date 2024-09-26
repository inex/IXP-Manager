<script>

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

    let myScaleTotal = function( data, type, full ) {

        if( type === 'sort' || type === 'type' ) {
            return data;
        }

        let strFormat = [ "B", "KB", "MB", "GB", "TB" ];
        // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
        // oData /= 8;

        let retString = "";

        for( let i = 0; i < strFormat.length; i++ )  {
            if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
                retString =  number_format( data, 0 ) + " " + strFormat[i];
                break;
            } else {
                data = data / 1000;
            }
        }

        return retString;
    };

    let tableList = $( '#ixpDataTable' );

    tableList.dataTable({
        stateSave: false,

        "aLengthMenu": [[20, 50, 100, 500, -1], [20, 50, 100, 500, "All"]],

        "bAutoWidth": false,

        "aaSorting": [[4, 'desc']],
        "iDisplayLength": 100,
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]},
            {"render": myScaleTotal, "aTargets": [2, 3, 4, 5, 6, 7, 8, 9, 10]}
        ]
    });


    $(document).ready(function() {
        tableList.show();
    });

</script>