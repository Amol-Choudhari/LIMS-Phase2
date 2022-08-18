<!-- Modal getmodal -->
<div class="modal fade" id="getmodal" tabindex="-1" role="dialog" aria-labelledby="getmodalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
    <div class="modal-content ">
      	<div class="modal-header ">
			<h5 class="modal-title " id="exampleModalLongTitle"> Z-Score <?php echo $_SESSION['user_flag']; ?> </h5>
			
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
							<?php
							foreach($result as $eachoff){ ?>
								<th scope="col"><?php echo $eachoff['ro_office']; ?></th>
							<?php
							}
							
							?>

						</tr>
					</thead>
					<tbody>
					<?php		

						if (isset($testarr)) {	

							$j=1;		
							$i=0;	
							foreach ($testarr as $eachtest) { ?>
							
							<tr>
								<td padding: 2px;><?php echo $j; ?></td>   
								<td><?php echo $testnames[$i]; ?> </td>
								<?php

									$l=0;
									foreach($smplList as $eachoff){
									?>
									<?php
										$num = (int) $zscorearr[$i][$l];
										$format = round($num, 2);
									?>
									
									<td><?php echo $format; ?> </td>

								<?php $l++;	} ?>

							</tr>
						
							

						
						<?php $i++; $j++; } } ?>
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


