<?php ?>

<input type="hidden" class="form-control" id="modal_type">
    <input type="hidden" class="form-control" id="formula">
		<div id="test_list">
			<div class="col-md-12 c11 dnone" id='color_mark'>
				<div class="row">
					<div class="col-md-6 offset-4">
						<span class="badge badge-pill bg-green"><b class="csuccess">w</b></span> - <strong>Test Reading Entered.</strong>

						<span class="badge badge-pill bg-orange"><b class="cpending">w</b></span> - <strong>Test Reading To Be Entered.</strong>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-xs-12 col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2 form-middle" id="data_table_div">
				<div class="table-responsive">
					<table class="table table-bordered dnone cdark" id="test_table">
						<thead class="fs14">
							<tr>
								<th>Sr.No</th>
								<th>Test</th>
								<!-- Select new column method name, to show method name in test list at chemist window, -->
								<th>Method Name</th>
								<th>Result</th>
								<th>Unit</th>
								<!--<th>Date Entered</th>-->
							</tr>
						</thead>
						<tbody class="fs14">

						</tbody>
					</table>
				</div>
			</div>
			<div class="col-xs-2 col-sm-1 col-md-1 row float-right dnone" id="finalize_div">
				<button id="finalize" class="btn btn-primary mt-2" title='This finalised button will be active once all test reading entered'>Finalize</button>
			</div>
		</div>
