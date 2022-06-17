$(document).ready(function () {
		$('#from_dt').datepicker({

			format: "dd/mm/yyyy",
			autoclose: true
		});

		$('#to_dt').datepicker({

			format: "dd/mm/yyyy",
			autoclose: true
		});

		$('#search').click(function(){

			if($('#from_dt').val()=='' || $('#to_dt').val()==''){
				alert('Please Select Proper Dates');
				return false;
			}
		});

	});
