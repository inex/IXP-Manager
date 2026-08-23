// IP Address
import { Address4, AddressError } from 'ip-address';
window.Address4 = Address4;
window.AddressError = AddressError;

// jQuery and UI
import $ from './init-jquery';
import select2 from 'select2';

window.$ = window.jQuery = $;
select2(window, $);

import 'jquery-ui-dist/jquery-ui.min.js';
import 'jquery-knob';

// Alias jQuery UI's tooltip to avoid overwriting Bootstrap
if (window.$.ui && window.$.ui.tooltip) {
    window.$.widget.bridge('uitooltip', window.$.ui.tooltip);
}

// Load Bootstrap AFTER jQuery UI (must be dynamic to preserve execution order)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Bootbox
import bootbox from 'bootbox';
window.bootbox = bootbox;

// Axios
import axios from 'axios';
window.axios = axios;

// DataTables
import 'datatables.net';
import 'datatables.net-bs4';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs4';

// Blueimp File Upload
await import( 'blueimp-file-upload/js/jquery.fileupload.js' );

// Moment
import moment from 'moment';
window.moment = moment;

// ClipboardJS
import ClipboardJS from 'clipboard';
window.ClipboardJS = ClipboardJS;

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// IXP Manager custom logic
import './ixp-functions.js';
await import( './ixp-manager.js' );
