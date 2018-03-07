@component('mail::message')
    Customer:   {{$cust->getName()}}

    Changed by: {{$user->getContact()->getName()}} <{{$user->getContact()->getEmail()}}>

    Action:     Note {{$type}}


    @if( $cn)

        ================= NEW NOTE DETAILS ===================

        Title:      {{$cn->getTitle()}}

        Visibility: @if( $cn->getPrivate() ) Private @else Public  @endif

        {{$cn->getNoteParsedown()}}

        ======================================================

    @endif

    @if( $ocn )

        ================= OLD NOTE DETAILS ===================

        Title:      {{$ocn->getTitle()}}

        Visibility: @if( $ocn->getPrivate()) Private @else Public @endif

        {{$ocn->getNoteParsedown()}}

        ======================================================

    @endif

@endcomponent