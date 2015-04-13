<?php
/*************************************************************************************************#
# www.rf-cloning.org
#
# Copyright (C) 2009-2014 Steve R. Bond <biologyguy@gmail.com>
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 3 as published by
# the Free Software Foundation <http://www.gnu.org/licenses/>
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#*************************************************************************************************/
?>
<div style="width:585px;">
<p>Please paste your sequencing result (in FASTA format) for <?php echo $_POST['proj_name']; ?> in the text area below.</p>
<form method="post" action="/cgi-bin/align_result.cgi" >
<textarea cols="70" rows="20" name="sequencing_seq" id="sequencing_seq"></textarea><br />
<input type="hidden" name="proj_name" value="<?php echo $_POST['proj_name']; ?>" />
<input type="hidden" name="plasmid_seq" value="<?php echo $_POST['plasmid_seq']; ?>" />
<input type="button" onclick="document.getElementById('sequencing_seq').value = '<?php echo $_POST['sequencing_seq']; ?>';" value="Example Sequence" />
<input type="submit" value="Run Alignment" style="float:right" />
</form>

</div>