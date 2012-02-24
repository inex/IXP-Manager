{if isset( $message )}

    <div class="alert alert-{$message->getClass()}">
        <a class="close" data-dismiss="alert">&times;</a>
        {$message->getMessage()}
    </div>

{/if}
