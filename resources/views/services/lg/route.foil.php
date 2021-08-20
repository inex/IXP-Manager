<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">
        Route Details - <code><?= $t->net ?></code>
        <?php if( $t->source === 'table' ): ?>
            in table <code><?= $t->name ?></code>
        <?php else: ?>
            as received from protocol <code><?= $t->name ?></code>
        <?php endif; ?>
    </h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <?php foreach( $t->content->routes as $r ): ?>
      <table class="table table-striped text-monospace" style="font-size: 14px;">
          <tbody>
              <tr>
                  <td>
                      <b>Network</b>
                  </td>
                  <td>
                      <?= $r->network ?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <b>Gateway</b>
                  </td>
                  <td>
                      <?= $r->gateway ?>
                      &nbsp;&nbsp;
                      <?php if( $r->primary ): ?>
                          <span class="badge badge-success">
                              PRIMARY
                          </span>
                      <?php else: ?>
                          <span class="badge badge-warning">
                              NOT PRIMARY
                          </span>
                      <?php endif; ?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <b>From Protocol</b>
                  </td>
                  <td>
                      <?= $r->from_protocol ?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <b>Age</b>
                  </td>
                  <td>
                      <?= isset( $r->age ) && $r->age ? date( "Y-m-d H:i:s", strtotime( $r->age ) ) : '' ?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <b>Metric</b>
                  </td>
                  <td>
                      <?= $r->metric ?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <b>Type</b>
                  </td>
                  <td>
                      <?= implode( ' ', $r->type ) ?>
                  </td>
              </tr>
              <?php if (isset( $r->bgp->as_path )): ?>
                  <tr>
                      <td>
                          <b>BGP :: AS Path</b>
                      </td>
                      <td>
                          <?= implode( ' ', $r->bgp->as_path ) ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->next_hop )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Next Hop</b>
                      </td>
                      <td>
                          <?= $r->bgp->next_hop ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->med )): ?>
                  <tr>
                      <td>
                          <b>BGP :: MED</b>
                      </td>
                      <td>
                          <?= $r->bgp->med ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->local_pref )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Local Pref</b>
                      </td>
                      <td>
                          <?= $r->bgp->local_pref ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->atomic_aggr )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Atomic Aggr</b>
                      </td>
                      <td>
                          <?= $r->bgp->atomic_aggr ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->aggregator )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Aggregator</b>
                      </td>
                      <td>
                          <?= $r->bgp->aggregator ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->communities )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Communities</b>
                      </td>
                      <td>
                          <?php foreach( $r->bgp->communities as $c ): ?>
                              <tt><?= implode(':',$c) ?></tt><br>
                          <?php endforeach; ?>
                      </td>
                  </tr>
              <?php endif; ?>
              <?php if (isset( $r->bgp->large_communities )): ?>
                  <tr>
                      <td>
                          <b>BGP :: Large Communities</b>
                      </td>
                      <td>
                          <?php foreach( $r->bgp->large_communities as $c ): ?>
                              <tt><?= implode(':',$c) ?></tt>

                              <?php if( $t->lg->router()->asn === $c[0] ): ?>
                                  <?php if( $lcinfo = $t->bird()->translateBgpFilteringLargeCommunity( ':' . $c[1] . ':' . $c[2] ) ): ?>
                                      <span class="badge badge-<?= $lcinfo[1] ?>"><?= $lcinfo[0] ?></span>
                                  <?php endif; ?>
                              <?php endif; ?>

                              <br>
                          <?php endforeach; ?>
                      </td>
                  </tr>
              <?php endif; ?>
          </tbody>
      </table>
    <br><br>
<?php endforeach; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>