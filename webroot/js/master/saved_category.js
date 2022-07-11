$(document).ready(function(){

	$('#category_list').DataTable();

	$('#category_list').on('click','#delete_record', function(e){

        if(confirm('Are you sure you want to delete this record ?')==false){
            e.preventDefault();
        }

    });

});


$(document).ready( function () {
    $('#pages_list_table').DataTable();
} );