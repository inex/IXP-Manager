
 <table class="table table-striped table-responsive-ixp collapse" style="width:100%">
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
     <?php foreach( $t->c->getConsoleServerConnections() as $csc ): ?>
         <tr>
             <td>
                 <?= $t->ee( $csc->getDescription() ) ?>
             </td>
             <td>
                 <?= $csc->getConsoleServer() ? $t->ee( $csc->getConsoleServer()->getCabinet()->getLocation()->getName() ) : "" ?>
             </td>
             <td>
                 <?= $csc->getConsoleServer() ? $t->ee( $csc->getConsoleServer()->getName() ) : "" ?>
             </td>
             <td>
                 <?= $t->ee( $csc->getPort() ) ?>
             </td>
         </tr>
     <?php endforeach; ?>
     </tbody>
 </table>



