
<div class="btn-group">
    <a class="btn btn-mini" href="{genUrl controller=$controller action="dashboard" id=$row.id}"><i class="icon-eye-open"></i></a>
    <a class="btn btn-mini" href="{genUrl controller=$controller action="edit" id=$row.id}"><i class="icon-pencil"></i></a>
    <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this record?' );" href="{genUrl controller=$controller action="delete" id=$row.id}"><i class="icon-trash"></i></a>
    <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
        More...
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a href="{genUrl controller='customer' action='send-welcome-email' id=$row.id}">Send Welcome Email</a>
        </li>
    </ul>
</div>
