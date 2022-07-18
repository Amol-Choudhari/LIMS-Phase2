<div id="ILC_list">
											<table id="ilc_avai_to_verify" class="table table-striped table-bordered table-hover">
												<thead class="tablehead">
													<tr>
														<th>Sr No</th>
														<th>Sample Code</th>
														<th>Commodity</th>
														<th>Type of Sample</th>
														<th>Office</th>
														<th>Submitted On</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													if (!empty($sample_codes1)) {

													$sr_no = 1;

													foreach ($sample_codes1 as $each) { ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php echo $each['submitted_on']; ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'redirect_to_grade_ilc', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'To Final Grading')); ?></td>
														</tr>
													<?php $sr_no++; } } ?>
												</tbody>
											</table>
										</div>