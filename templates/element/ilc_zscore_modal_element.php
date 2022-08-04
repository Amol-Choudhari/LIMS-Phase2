<!-- Modal getmodal -->
<div class="modal fade" id="getmodal" tabindex="-1" role="dialog" aria-labelledby="getmodalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle"> Z-Score <?php echo $_SESSION['user_flag']; ?> </h5>
			
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
      	</div>
		<?php echo $this->Form->create(); ?>
			<div class="modal-body">
			
				<div class="row">
					<div class="col-md-3">
						<P><b> Org Sample Code </b> <?php echo $getcommodity['org_sample_code']; ?></p>
					</div>
					<div class="col-md-3">
						<P><b> Category </b> <?php echo $getcommodity['category_name']; ?></p>
					</div>
					<div class="col-md-3">
						<P><b> Commodity </b> <?php echo $getcommodity['commodity_name']; ?>   </p>
					</div>
					<div class="col-md-3">
						<P><b> Sample Type </b> <?php echo $getcommodity['sample_type_desc']; ?></p>
					</div>
				</div>
			
				<table class="table table-bordered">
					<thead>
						<tr>
						<th scope="col">Sr.No</th>
						<th scope="col">Test</th>
						<th scope="col">Z-score</th>
						<th scope="col">Put Z-score Outlier Value</th>
						</tr>
					</thead>
					<tbody>
					<?php		

						if (isset($result3)) {	

							$j=1;		
							$i=0;	
							foreach ($result3 as $res) { ?>
							
							<tr>
								<td><?php echo $j; ?></td>   
								<td><?php echo $res[0]['test_name']; ?> </td>	
								<td>2</td> 
								<td><input type="text"  class="form-control" id="zscore" name="zscore" value="2"></td> 
							</tr>

						<?php $j++; $i++; }}?>
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary" id="save_zscore" name="save_zscore">Save changes</button>
			</div>
		<?php echo $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Modal end-->