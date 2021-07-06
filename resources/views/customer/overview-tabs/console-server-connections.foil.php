<?php
    $c = $t->c; /** @var $c \IXP\Models\Customer */
?>

<table class="table table-striped table-responsive-ixp collapse w-100">
   <thead class="thead-dark">
       <tr>
           <th>
               Description
           </th>
           <th>
               Location
           </th>
           <th>
               Console Server
           </th>
           <th>
               Port
           </th>
       </tr>
   </thead>
   <tbody>
       <?php foreach( $c->consoleServerConnections as $csc ):
           $consoleServer = $csc->consoleServer;
           ?>
           <tr>
               <td>
                   <?= $t->ee( $csc->description ) ?>
               </td>
               <td>
                   <?= $consoleServer ? $t->ee( $consoleServer->cabinet->location->name ) : "" ?>
               </td>
               <td>
                   <?= $consoleServer ? $t->ee( $consoleServer->name ) : "" ?>
               </td>
               <td>
                   <?= $t->ee( $csc->port ) ?>
               </td>
           </tr>
       <?php endforeach; ?>
   </tbody>
</table>