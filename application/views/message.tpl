{if isset( $message )}

<div id="oss-message-2">
    <div class="oss-message oss-message-{$message->getClass()}">
        {$message->getMessage()}
        <div class="oss-message-icon">
            <div class="oss-message-icon-2">
                <div id="oss-message-close-icon-2" class="ui-state-default ui-corner-all" title="Close">
                    <span class="ui-icon ui-icon-close"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
{literal}
function bindOSSErrorActions()
{
    $("div[id^='oss-message-close-icon-']").hover(
        function () {
            var theid= '2'; //$(this).attr('id').substr(23);
            $("div[id='oss-message-close-icon-" + theid + "'] > span").addClass( 'ui-state-hover' );
          },
          function () {
            var theid= '2'; //$(this).attr('id').substr(23);
            $("div[id='oss-message-close-icon-" + theid + "'] > span").removeClass( 'ui-state-hover' );
          }
    );

    $("div[id^='oss-message-close-icon-']").click(function () {
        var theid= '2'; //$(this).attr('id').substr(23);
        $("div[id='oss-message-" + theid + "']").hide("slow", function(){
                  $("div[id='oss-message-" + theid + "']").remove()
        });
    });
}

$(document).ready(function(){
    bindOSSErrorActions();
});

{/literal}
</script>

{/if}

