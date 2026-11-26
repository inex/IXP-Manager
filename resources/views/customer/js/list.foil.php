<script type="module">
        $( document ).ready( function() {
            const filterRow = $( '#filter-row' );
            const filterPanelStateKey = 'customer-list-filter-options-open';
            const showColumnsStateKey = 'customer-list-show-columns-open';
            const showColumns = $( '#show-columns-options' );
            const multiFilters = $( ".customer-multi-filter" );
            const updateMultiFilterStyle = function( filter ) {
                const active = ( filter.val() || [] ).length > 0;
                const selection = filter.next( ".select2" ).find( ".select2-selection" );

                selection.toggleClass( "border-primary", active );
                selection.find( ".select2-selection__rendered, .select2-selection__choice" )
                    .toggleClass( "text-primary font-weight-bold", active );
            };

            multiFilters.each( function() {
                const filter = $( this );
                filter.on( "change", function() {
                    updateMultiFilterStyle( filter );
                } );
            } );
            window.setTimeout( function() {
                multiFilters.each( function() {
                    updateMultiFilterStyle( $( this ) );
                } );
            }, 0 );

            if( <?= $t->clearFilters ? "true" : "false" ?> ) {
                localStorage.setItem( showColumnsStateKey, '0' );
            }

            if( !<?= $t->clearFilters ? "true" : "false" ?> && localStorage.getItem( showColumnsStateKey ) === '1' ) {
                showColumns.addClass( 'show' );
                $( '#show-columns-toggle' ).removeClass( 'collapsed' ).attr( 'aria-expanded', 'true' );
                $( '#show-columns-twisty' ).removeClass( 'fa-caret-right' ).addClass( 'fa-caret-down' );
            }

            const table = $( '#customer-list' ).DataTable( {
                responsive: true,
                stateSave: true,
                stateDuration: DATATABLE_STATE_DURATION,
                <?php if( $t->clearFilters ): ?>
                    stateLoadParams: function( settings, data ) {
                        const defaultVisibleColumns = [ 0, 5, 8, 9, 11, 12, 21 ];

                        data.columns.forEach( function( column, index ) {
                            column.visible = defaultVisibleColumns.includes( index );
                        });
                    },
                <?php endif; ?>
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                    { visible: <?= $t->showCurrentOnly ? "false" : "true" ?>, targets: 13 },
                    { visible: false, targets: [ 1, 2, 3, 4, 6, 7, 10, 14, 15, 16, 17, 18, 19, 20 ] }
                ],
            } );

            table.column( 13 ).visible( <?= $t->showCurrentOnly ? "false" : "true" ?> );
            <?php if( $t->clearFilters ): ?>
                table.state.save();
            <?php endif; ?>
            $( '#customer-list' ).show();
            showColumns
                .on( "shown.bs.collapse", function() {
                    $( "#show-columns-twisty" ).removeClass( "fa-caret-right" ).addClass( "fa-caret-down" );
                    localStorage.setItem( showColumnsStateKey, "1" );
                })
                .on( "hidden.bs.collapse", function() {
                    $( "#show-columns-twisty" ).removeClass( "fa-caret-down" ).addClass( "fa-caret-right" );
                    localStorage.setItem( showColumnsStateKey, "0" );
                });

            $( "#clear-customer-filters" ).on( "click", function() {
                localStorage.setItem( showColumnsStateKey, "0" );
            });

            $( "#tag-filter-options" )
                .on( "show.bs.collapse", function() {
                    $( "#tag-filter-twisty" ).removeClass( "fa-caret-right" ).addClass( "fa-caret-down" );
                })
                .on( "hide.bs.collapse", function() {
                    $( "#tag-filter-twisty" ).removeClass( "fa-caret-down" ).addClass( "fa-caret-right" );
                });

            $( "#customer-filter-form" ).on( "submit", function() {
                $( this ).find( "select[name=\"include-tags[]\"], select[name=\"exclude-tags[]\"]" )
                    .filter( function() {
                        return $( this ).val() === "0";
                    })
                    .prop( "disabled", true );
            });

            const ensureTrailingTagFilter = function( container ) {
                let entries = container.find( ".tag-filter-entry" );
                let emptyEntries = entries.filter( function() {
                    return $( this ).find( "select" ).val() === "0";
                } );

                if( emptyEntries.length === 0 ) {
                    const entry = entries.first().clone();
                    entry.find( "select" ).val( "0" );
                    container.append( entry );
                    return;
                }

                emptyEntries.slice( 0, -1 ).remove();
                entries = container.find( ".tag-filter-entry" );
                emptyEntries = entries.filter( function() {
                    return $( this ).find( "select" ).val() === "0";
                } );
                container.append( emptyEntries.last() );
            };

            $( "#include-tag-filters, #exclude-tag-filters" ).each( function() {
                ensureTrailingTagFilter( $( this ) );
            } );

            $( "#tag-filter-options" )
                .on( "change", ".tag-filter-entry select", function() {
                    ensureTrailingTagFilter( $( this ).closest( "[id$=tag-filters]" ) );
                } )
                .on( "click", ".remove-tag-filter", function() {
                    const container = $( this ).closest( "[id$=tag-filters]" );
                    const entries = container.find( ".tag-filter-entry" );

                    if( entries.length === 1 ) {
                        entries.find( "select" ).val( "0" );
                    } else {
                        $( this ).closest( ".tag-filter-entry" ).remove();
                    }

                    ensureTrailingTagFilter( container );
                } );

            $( "#clear-tag-filters" ).on( "click", function() {
                $( "#include-tag-filters, #exclude-tag-filters" ).each( function() {
                    const entries = $( this ).find( ".tag-filter-entry" );
                    entries.slice( 1 ).remove();
                    entries.first().find( "select" ).val( "0" );
                });
            });


            const customerExportData = function() {
                const columns = table.columns( ':visible' ).indexes().toArray()
                    .filter( function( column ) {
                        return column !== 21;
                    });
                const headers = columns.map( function( column ) {
                    return $( table.column( column ).header() ).text().trim();
                });
                const rows = table.rows( { search: 'applied' } ).indexes().toArray().map( function( row ) {
                    return columns.map( function( column ) {
                        const value = table.cell( row, column ).render( 'display' );

                        return $( '<div>' ).html( value === null ? '' : value ).text().replace( /\s+/g, ' ' ).trim();
                    });
                });

                return { headers: headers, rows: rows };
            };

            const downloadCustomerExport = function( contents, mimeType, extension ) {
                const blob = new Blob( [ contents ], { type: mimeType } );
                const url = URL.createObjectURL( blob );
                const link = document.createElement( 'a' );
                const date = new Date().toISOString().slice( 0, 10 );

                link.href = url;
                link.download = 'customers-' + date + '.' + extension;
                document.body.appendChild( link );
                link.click();
                link.remove();
                window.setTimeout( function() {
                    URL.revokeObjectURL( url );
                }, 1000 );
            };

            $( '#export-customer-csv' ).on( 'click', function() {
                const exportData = customerExportData();
                const csvValue = function( value ) {
                    const safeValue = /^[=+\-@]/.test( value ) ? "'" + value : value;

                    return '"' + safeValue.replace( /"/g, '""' ) + '"';
                };
                const csv = [ exportData.headers ].concat( exportData.rows )
                    .map( function( row ) {
                        return row.map( csvValue ).join( ',' );
                    })
                    .join( '\r\n' );

                downloadCustomerExport( '\uFEFF' + csv, 'text/csv;charset=utf-8', 'csv' );
            });

            $( '#export-customer-json' ).on( 'click', function() {
                const exportData = customerExportData();
                const records = exportData.rows.map( function( row ) {
                    const record = {};

                    exportData.headers.forEach( function( header, index ) {
                        record[ header ] = row[ index ];
                    });

                    return record;
                });

                downloadCustomerExport( JSON.stringify( records, null, 2 ), 'application/json;charset=utf-8', 'json' );
            });

            $( '.customer-column-toggle' ).each( function() {
                const column = table.column( $( this ).data( 'column' ) );
                $( this ).prop( 'checked', column.visible() );
            } ).on( 'change', function() {
                table.column( $( this ).data( 'column' ) ).visible( this.checked );
                table.columns.adjust().responsive.recalc();
                table.state.save();
            });

            if( localStorage.getItem( filterPanelStateKey ) === '1' ) {
                filterRow.show();
            }

            $( '#btn-filter-options' ).click( function( e ) {
                e.preventDefault();

                filterRow.stop( true, true ).slideToggle( 200, function() {
                    localStorage.setItem(
                        filterPanelStateKey,
                        filterRow.is( ':visible' ) ? '1' : '0'
                    );
                });
            });
        });
    </script>
