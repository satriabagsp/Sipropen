<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">   
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
 
<script type="text/javascript" class="init">
	$(document).ready(function() {
	var table = $('#example').DataTable();
 
	$('#example tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		alert( 'You clicked on '+data[0]+'\'s row' );
	} );
</script>

