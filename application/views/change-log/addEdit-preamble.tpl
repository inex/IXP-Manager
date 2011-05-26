
<link rel="stylesheet" href="{genUrl}/js/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<script type="text/javascript" src="{genUrl}/js/jwysiwyg/jquery.wysiwyg.js"></script>

<script type="text/javascript">
    $(function()
    {ldelim}
        $('#details').wysiwyg();

        $('#livedate').datepicker({ldelim}
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        {rdelim});
    {rdelim});
</script>
