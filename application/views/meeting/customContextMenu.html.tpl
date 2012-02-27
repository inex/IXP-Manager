

<div class="btn-group">
    <a class="btn btn-mini" href="{genUrl controller=$controller action="edit" id=$row.id}"><i class="icon-pencil"></i></a>
    <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this record?' );" href="{genUrl controller=$controller action="delete" id=$row.id}"><i class="icon-trash"></i></a>

    <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
        More...
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a href="{genUrl controller='meeting-item' action='list' id=$row.id}">See Presentations...</a>
        </li>
        <li>
            <a href="{genUrl controller='meeting' action='compose' id=$row.id}">Compose Email...</a>
        </li>
    </ul>
</div>
