<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <button id="export-customer-csv" type="button" class="btn btn-white" title="Export visible columns as CSV">
            CSV
        </button>
        <button id="export-customer-json" type="button" class="btn btn-white" title="Export visible columns as JSON">
            JSON
        </button>
        <a id="btn-filter-options" class="btn btn-white" href="<?= url()->current() ?>">
            Filter Options
        </a>
        <a class="btn btn-white" href="<?= route( 'customer@create' ) ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <style>
        .customer-filter-label {
            color: rgba( 0, 0, 0, 0.5 );
            font-size: 0.9rem;
            line-height: 1.2;
        }

        .customer-filter-label.mb-1 {
            margin-bottom: calc( 0.25rem + 1px ) !important;
        }

        .customer-multi-filter + .select2-container .select2-selection--multiple {
            min-height: 38px;
            padding: 2px 4px 4px;
            border-color: #ced4da;
        }

        .customer-multi-filter + .select2-container .select2-selection__choice {
            min-height: 28px;
            margin-top: 3px;
            padding: 2px 8px 2px 26px;
            color: #0056b3;
            background-color: #e7f1ff;
            border-color: #7abaff;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .customer-multi-filter + .select2-container .select2-selection__choice__remove {
            min-height: 26px;
            padding: 2px 6px;
            color: #0056b3;
            border-color: #7abaff;
        }

        .customer-multi-filter + .select2-container .select2-search__field {
            height: 26px;
            font-size: 1rem;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <nav id="filter-row" class="collapse navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <span class="navbar-brand">Filter Options:</span>
                <button class="navbar-toggler float-right" type="button" data-toggle="collapse" data-target="#customer-filter-options" aria-controls="customer-filter-options" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="customer-filter-options">
                    <div class="w-100">
                        <form id="customer-filter-form" class="navbar-form navbar-left w-100" method="get" action="<?= route( 'customer@list' ) ?>">
                            <input type="hidden" name="filters-submitted" value="1">
                            <ul class="navbar-nav flex-wrap">
                            <li class="nav-item">
                                <div class="nav-link d-flex flex-column align-items-start" style="min-width: 260px;">
                                    <label class="customer-filter-label font-weight-bold mb-1" for="customer-state-filter">State</label>
                                    <select id="customer-state-filter" name="state[]" class="form-control chzn-select customer-multi-filter" multiple data-placeholder="All States">
                                        <?php foreach( \IXP\Models\Customer::$CUST_STATUS_TEXT as $state => $text ): ?>
                                            <option value="<?= $state ?>" <?= in_array( $state, $t->states, true ) ? 'selected' : '' ?>><?= $t->ee( $text ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex flex-column align-items-start" style="min-width: 220px;">
                                    <label class="customer-filter-label font-weight-bold mb-1" for="customer-type-filter">Type</label>
                                    <select id="customer-type-filter" name="type[]" class="form-control chzn-select customer-multi-filter" multiple data-placeholder="All Types">
                                        <?php foreach( \IXP\Models\Customer::$CUST_TYPES_TEXT as $type => $text ): ?>
                                            <option value="<?= $type ?>" <?= in_array( $type, $t->types, true ) ? 'selected' : '' ?>><?= $t->ee( $text ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex flex-column align-items-start" style="min-width: 240px;">
                                    <label class="customer-filter-label font-weight-bold mb-1" for="customer-peering-policy-filter">Peering Policy</label>
                                    <select id="customer-peering-policy-filter" name="peering-policy[]" class="form-control chzn-select customer-multi-filter" multiple data-placeholder="All Peering Policies">
                                        <?php foreach( \IXP\Models\Customer::$PEERING_POLICIES as $policy => $text ): ?>
                                            <option value="<?= $policy ?>" <?= in_array( $policy, $t->peeringPolicies, true ) ? 'selected' : '' ?>><?= $t->ee( ucfirst( $text ) ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex flex-column align-items-start" style="min-width: 220px;">
                                    <label class="customer-filter-label font-weight-bold mb-1" for="customer-route-server-filter">Route Server</label>
                                    <select id="customer-route-server-filter" name="route-server" class="form-control <?= count( $t->routeServers ) ? "border-primary text-primary font-weight-bold" : "" ?>">
                                        <option value="">All</option>
                                        <option value="yes" <?= $t->routeServers === [ "yes" ] ? "selected" : "" ?>>Yes</option>
                                        <option value="no" <?= $t->routeServers === [ "no" ] ? "selected" : "" ?>>No</option>
                                    </select>
                                </div>
                            </li>


                            <li class="nav-item">
                                <div class="nav-link d-flex flex-column align-items-start h-100">
                                    <span class="customer-filter-label mb-1" aria-hidden="true">&nbsp;</span>
                                    <label class="mb-0 d-flex align-items-center <?= !$t->showCurrentOnly ? "text-primary font-weight-bold" : "" ?>" style="min-height: 38px;">
                                        <input name="current-only" value="1" class="mr-2 mt-0" type="checkbox" <?= $t->showCurrentOnly ? 'checked' : '' ?>>
                                        Current only
                                    </label>
                                </div>
                            </li>

                            </ul>
                            <?php if( $t->resellerMode() ): ?>
                                <div class="d-flex flex-wrap align-items-end border-top mt-2 pt-2 px-2">
                                    <div class="d-flex flex-column align-items-start mr-3" style="min-width: 260px;">
                                        <label class="customer-filter-label font-weight-bold mb-1" for="customer-reseller-type-filter">Resold/Reseller</label>
                                        <select id="customer-reseller-type-filter" name="reseller-type[]" class="form-control chzn-select customer-multi-filter" multiple data-placeholder="All Customer Categories">
                                            <option value="reseller" <?= in_array( "reseller", $t->resellerTypes, true ) ? "selected" : "" ?>>Reseller</option>
                                            <option value="resold" <?= in_array( "resold", $t->resellerTypes, true ) ? "selected" : "" ?>>Resold</option>
                                            <option value="other" <?= in_array( "other", $t->resellerTypes, true ) ? "selected" : "" ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="d-flex flex-column align-items-start ml-auto" style="min-width: 260px;">
                                        <label class="customer-filter-label font-weight-bold mb-1" for="customer-reseller-filter">Reseller</label>
                                        <select id="customer-reseller-filter" name="reseller" class="form-control <?= $t->resellerId ? "border-primary text-primary font-weight-bold" : "" ?>">
                                            <option value="0">All Resellers</option>
                                            <?php foreach( $t->resellers as $reseller ): ?>
                                                <option value="<?= $reseller->id ?>" <?= (int)$t->resellerId === $reseller->id ? "selected" : "" ?>><?= $t->ee( $reseller->name ) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row border-top mt-2 pt-2 px-2">
                                <div class="col-lg-6 mb-2">
                                    <label class="customer-filter-label font-weight-bold mb-1 d-block">Joined</label>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <label class="mb-0 mr-2" for="joined-from">From</label>
                                        <input id="joined-from" name="joined-from" type="date" class="form-control form-control-sm w-auto mr-3 <?= $t->joinedFrom ? "border-primary text-primary font-weight-bold" : "" ?>" value="<?= $t->ee( $t->joinedFrom ?: "" ) ?>">
                                        <label class="mb-0 mr-2" for="joined-to">To</label>
                                        <input id="joined-to" name="joined-to" type="date" class="form-control form-control-sm w-auto <?= $t->joinedTo ? "border-primary text-primary font-weight-bold" : "" ?>" value="<?= $t->ee( $t->joinedTo ?: "" ) ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="customer-filter-label font-weight-bold mb-1 d-block">Left</label>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <label class="mb-0 mr-2" for="left-from">From</label>
                                        <input id="left-from" name="left-from" type="date" class="form-control form-control-sm w-auto mr-3 <?= $t->leftFrom ? "border-primary text-primary font-weight-bold" : "" ?>" value="<?= $t->ee( $t->leftFrom ?: "" ) ?>">
                                        <label class="mb-0 mr-2" for="left-to">To</label>
                                        <input id="left-to" name="left-to" type="date" class="form-control form-control-sm w-auto <?= $t->leftTo ? "border-primary text-primary font-weight-bold" : "" ?>" value="<?= $t->ee( $t->leftTo ?: "" ) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-top mt-2 pt-2 px-2">
                                <button class="btn btn-link p-0 <?= $t->filterTags ? "border border-primary rounded px-2 font-weight-bold" : "collapsed" ?>" type="button" data-toggle="collapse" data-target="#tag-filter-options" aria-expanded="<?= $t->filterTags ? "true" : "false" ?>" aria-controls="tag-filter-options">
                                    <i id="tag-filter-twisty" class="fa <?= $t->filterTags ? "fa-caret-down" : "fa-caret-right" ?> mr-2"></i>
                                    <strong>Filter tags</strong>
                                </button>
                            </div>

                            <div id="tag-filter-options" class="collapse <?= $t->filterTags ? "show" : "" ?>">
                                <div class="row border-top mt-2 pt-2 px-2">
                                    <div class="col-md-5 mb-2">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="customer-filter-label font-weight-bold mr-2 mb-0">Include tags</span>
                                            <select name="include-tags-mode" class="form-control form-control-sm w-auto <?= $t->includeTagsMode === "any" ? "border-primary text-primary font-weight-bold" : "" ?>">
                                                <option value="all">Match all</option>
                                                <option value="any" <?= $t->includeTagsMode === "any" ? "selected" : "" ?>>Match any</option>
                                            </select>
                                        </div>
                                        <div id="include-tag-filters">
                                            <?php foreach( $t->includeTags ?: [ 0 ] as $tagId ): ?>
                                                <div class="input-group input-group-sm mb-2 tag-filter-entry">
                                                    <select name="include-tags[]" class="form-control">
                                                        <option value="0">Choose a tag...</option>
                                                        <?php foreach( $t->tags as $tag ): ?>
                                                            <option value="<?= $tag[ "id" ] ?>" <?= (int)$tagId === $tag[ "id" ] ? "selected" : "" ?>><?= $t->ee( $tag[ "display_as" ] ) ?> (<?= $t->ee( $tag[ "tag" ] ) ?>)</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-white remove-tag-filter" title="Remove tag"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-5 mb-2">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="customer-filter-label font-weight-bold mr-2 mb-0">Exclude tags</span>
                                            <select name="exclude-tags-mode" class="form-control form-control-sm w-auto <?= $t->excludeTagsMode === "all" ? "border-primary text-primary font-weight-bold" : "" ?>">
                                                <option value="any">Match any</option>
                                                <option value="all" <?= $t->excludeTagsMode === "all" ? "selected" : "" ?>>Match all</option>
                                            </select>
                                        </div>
                                        <div id="exclude-tag-filters">
                                            <?php foreach( $t->excludeTags ?: [ 0 ] as $tagId ): ?>
                                                <div class="input-group input-group-sm mb-2 tag-filter-entry">
                                                    <select name="exclude-tags[]" class="form-control">
                                                        <option value="0">Choose a tag...</option>
                                                        <?php foreach( $t->tags as $tag ): ?>
                                                            <option value="<?= $tag[ "id" ] ?>" <?= (int)$tagId === $tag[ "id" ] ? "selected" : "" ?>><?= $t->ee( $tag[ "display_as" ] ) ?> (<?= $t->ee( $tag[ "tag" ] ) ?>)</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-white remove-tag-filter" title="Remove tag"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2 mb-2 d-flex align-items-start">
                                        <button id="clear-tag-filters" type="button" class="btn btn-sm btn-white">Clear all</button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end border-top mt-2 pt-2 px-3">
                                <a id="clear-customer-filters" class="btn btn-white ml-2" href="<?= route( "customer@list", [ "clear-filters" => 1 ] ) ?>">Clear</a>
                                <button type="submit" class="btn btn-white ml-2">Apply Filters</button>
                            </div>
                        </form>

                        <div class="border-top mt-2 py-2 px-3" style="background-color: #e2e3e5;">
                            <button id="show-columns-toggle" class="btn btn-link p-0 collapsed" type="button" data-toggle="collapse" data-target="#show-columns-options" aria-expanded="false" aria-controls="show-columns-options">
                                <i id="show-columns-twisty" class="fa fa-caret-right mr-2"></i>
                                <strong>Show/Hide Columns</strong>
                            </button>
                            <div id="show-columns-options" class="collapse mt-2">
                                <?php foreach( [
                                [ 0 => "Name", 1 => "Registered Name", 2 => "Abbreviated Name", 3 => "Short Name", 4 => "Company Number", 16 => "Jurisdiction", 17 => "Country" ],
                                [ 5 => "AS", 9 => "Type", 11 => "Status", 12 => "Joined", 13 => "Left", 15 => "Updated", 10 => "Reseller" ],
                                [ 8 => "Peering Policy", 18 => "MANRS", 19 => "PeeringDB", 20 => "Route Server" ],
                                [ 7 => "Peering Email", 6 => "NOC Email" ],
                                [ 14 => "Tags", 21 => "Action" ],
                            ] as $columnGroup ): ?>
                                <div class="d-flex flex-wrap mb-1">
                                    <?php foreach( $columnGroup as $column => $label ): ?>
                                        <label class="mr-3 mb-0">
                                            <input type="checkbox" class="mr-1 customer-column-toggle" data-column="<?= $column ?>" checked>
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <?= $t->alerts() ?>
        <table id='customer-list' class="table collapse table-striped no-wrap responsive tw:shadow-md w-100" >
            <thead class="thead-dark">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Registered Name
                    </th>
                    <th>
                        Abbreviated Name
                    </th>
                    <th>
                        Short Name
                    </th>
                    <th>
                        Company Number
                    </th>
                    <th>
                        AS
                    </th>
                    <th>
                        NOC Email
                    </th>
                    <th>
                        Peering Email
                    </th>
                    <th>
                        Peering Policy
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Reseller
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Joined
                    </th>
                    <th>
                        Left
                    </th>
                    <th>
                        Tags
                    </th>
                    <th>
                        Updated
                    </th>
                    <th>
                        Jurisdiction
                    </th>
                    <th>
                        Country
                    </th>
                    <th>
                        MANRS
                    </th>
                    <th>
                        PeeringDB
                    </th>
                    <th>
                        Route Server
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach( $t->custs as $c ):
                /** @var \IXP\Models\Customer $c */
                ?>
                <tr>
                    <td>
                        <a href="<?= route( "customer@overview" , [ 'cust' => $c->id ] ) ?>">
                            <?= $t->ee( $c->name ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $c->companyRegisteredDetail?->registeredName ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->abbreviatedName ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->shortname ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->companyRegisteredDetail?->companyNumber ) ?>
                    </td>
                    <td>
                        <?php if( $c->autsys ): ?>
                            <a href="#">
                                <?=  $t->asNumber( $c->autsys ) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( $c->nocemail ): ?>
                            <a href="mailto:<?= $t->ee( $c->nocemail ) ?>"><?= $t->ee( $c->nocemail ) ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( $c->peeringemail ): ?>
                            <a href="mailto:<?= $t->ee( $c->peeringemail ) ?>"><?= $t->ee( $c->peeringemail ) ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->peeringpolicy ) ?>
                    </td>
                    <!-- <td>
                        <?= $c->reseller ? "Yes" : "No" ?>
                    </td> -->
                    <td>
                        <?= $t->insert( 'customer/list-type',   [ 'cust' => $c ] ) ?>
                    </td>
                    <td>
                        <?php if( $r = $c->resellerObject ): ?>
                            <a href="<?= route( 'customer@overview', [ 'cust' => $r->id ] ) ?>">
                                <?= $t->ee( $r->abbreviatedName ) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $t->insert( 'customer/list-status', [ 'cust' => $c ] ) ?>
                    </td>
                    <td>
                        <?= \Carbon\Carbon::parse( $c->datejoin )->format( 'Y-m-d' ) ?>
                    </td>
                    <td>
                        <?php if( $c->dateleave && \Carbon\Carbon::parse( $c->dateleave )->format( "Y-m-d" ) !== "0000-00-00" && \Carbon\Carbon::parse( $c->dateleave )->format( "Y-m-d" ) !== "-0001-11-30" ): ?>
                            <?= \Carbon\Carbon::parse( $c->dateleave )->format( "Y-m-d" ) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php foreach( $c->tags as $tag ): ?>
                            <span class="badge badge-secondary"><?= $t->ee( $tag->tag ) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?= $c->updated_at ? $c->updated_at->format( "Y-m-d" ) : "" ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->companyRegisteredDetail?->jurisdiction ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->companyRegisteredDetail?->country ) ?>
                    </td>
                    <td>
                        <?php if( $c->in_manrs ): ?>
                            <a href="https://www.manrs.org/" target="_blank">Yes</a>
                        <?php else: ?>
                            No
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( $c->in_peeringdb && $c->autsys ): ?>
                            <a href="https://www.peeringdb.com/asn/<?= $c->autsys ?>" target="_peeringdb">Yes</a>
                        <?php else: ?>
                            No
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( \Carbon\Carbon::parse( $c->datejoin )->lte( \Carbon\Carbon::today() ) && ( !$c->dateleave || \Carbon\Carbon::parse( $c->dateleave )->gte( \Carbon\Carbon::today() ) ) ): ?>
                            <?= $c->route_server_client ? "Yes" : "No" ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ 'cust' => $c->id ] ) ?>" title="Overview">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a class="btn btn-white" href="<?= route ( "customer@delete-recap", [ "cust" => $c->id ] )   ?>" title="Delete">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/js/list', [
        'clearFilters'    => $t->clearFilters,
        'showCurrentOnly' => $t->showCurrentOnly,
    ] ); ?>
<?php $this->append() ?>
