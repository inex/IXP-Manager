<div id="peeringNotesDialog" style="display: none">
    <form class="form" id="peeringNotesForm" method="post" action="{genUrl controller=dashboard action='my-peering-matrix-notes' save=1}">
    <div>
	    <h2>
	    	<span id="peeringNotesDialog-member">Loading...</span>
		    <span style="float: right"><input id="notes-save" type="submit" name="submit" value="save" /></span>
    	</h2>
    </div>
    <p align="center">
        <textarea id="peeringNotesDialog-notes" name="notes" cols="60" rows="10" class="fixedFont">Loading...</textarea>
        <input id="peeringNotesDialog-id" type="hidden" name="id" value="" />
    </p>
    </form>
</div>


