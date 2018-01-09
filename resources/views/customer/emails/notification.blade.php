Customer:   {{$cust->getName()}}
<br>
Changed by: {{$user->getContact()->getName()}} <{{$user->getContact()->getEmail()}}>
<br>
Action:     Note @if( !$old) ADDED @elseif( !$new )DELETED @else UPDATED @endif
<br>

@if( $new)

    ================= NEW NOTE DETAILS ===================
    <br>
    Title:      {{$new->getTitle()}}
    <br>
    Visibility: @if( $new->getPrivate() ) Private @else Public  @endif
    <br>
    {{$new->getNoteParsedown()}}
    <br>
    ======================================================

@endif

<br>

@if( $old )

    ================= OLD NOTE DETAILS ===================
    <br>
    Title:      {{$old->getTitle()}}
    <br>
    Visibility: @if( $old->getPrivate()) Private @else Public @endif
    <br>
    {{$old->getNoteParsedown()}}
    <br>
    ======================================================

@endif