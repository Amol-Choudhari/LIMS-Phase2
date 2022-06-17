<?php ?>



		<script>
		$(document).ready(function(){
			
			$('ul.tabs li').click(function(){
				var tab_id = $(this).attr('data-tab');
				
				$('.allocation-page').hide();
				$('.inspection').hide();

				$('ul.tabs li').removeClass('current');
				$('.tab-content').removeClass('current');

				$(this).addClass('current');
				$("#"+tab_id).addClass('current');
				
				//$('.table-format td').addClass('display_none');
			})
			

			$('ul.tabs li').addClass('current');

		})
	</script>
	
	
	
	

	
	
	
	
	
	<!-- For Inspection window -->
	
	
	<?php if($this->params['controller'] == 'inspections') { 
	
		if($this->params['action'] == 'pending_applications' || $this->params['action'] == 'renewal_pending_applications' ||
			$this->params['action'] == 'referred_back_applications' || $this->params['action'] == 'renewal_referred_back_applications' ||
			$this->params['action'] == 'replied_applications' || $this->params['action'] == 'renewal_replied_applications' ||
			$this->params['action'] == 'verified_applications' || $this->params['action'] == 'renewal_verified_applications'){ ?>
	
				<script>
					$(document).ready(function(){
						
							$('ul.tabs li').addClass('current');
							$('#tab-1-content').addClass('current');

					})
				</script>
				
	
			<?php }} if($this->params['controller'] == 'siteinspections'){?>
	
		<script>
		$(document).ready(function(){
			
				$('#tab-2').addClass('current');
				$('#tab-2-content').addClass('current');	

		})
		</script>
		
		
	<?php } if($this->params['controller'] == 'roinspections'){?>
	
		<script>
		$(document).ready(function(){
			
				$('#tab-3').addClass('current');
				$('#tab-3-content').addClass('current');	

		})
		</script>
		
	<?php } if($this->params['controller'] == 'hoinspections'){
		
				if($this->params['action'] == 'dyama_pending' ||
					$this->params['action'] == 'dyama_commented'||
					$this->params['action'] == 'dyama_replied'){ ?>
	
					<script>
					$(document).ready(function(){
						
							$('#tab-4').addClass('current');
							$('#tab-4-content').addClass('current');	

					})
					</script>
		
			<?php } if($this->params['action'] == 'ho_mo_pending' ||
						$this->params['action'] == 'ho_mo_commented'||
						$this->params['action'] == 'ho_mo_replied'){ ?>
	
					<script>
					$(document).ready(function(){
						
							$('#tab-5').addClass('current');
							$('#tab-5-content').addClass('current');	

					})
					</script>
		
			<?php } if($this->params['action'] == 'ho_jtama_pending' ||
						$this->params['action'] == 'ho_jtama_commented'||
						$this->params['action'] == 'ho_jtama_replied'){ ?>
	
					<script>
					$(document).ready(function(){
						
							$('#tab-6').addClass('current');
							$('#tab-6-content').addClass('current');	

					})
					</script>
		
			<?php } if($this->params['action'] == 'ho_ama_pending' ||
						$this->params['action'] == 'ho_ama_commented'||
						$this->params['action'] == 'ho_ama_replied'){ ?>
	
					<script>
					$(document).ready(function(){
						
							$('#tab-7').addClass('current');
							$('#tab-7-content').addClass('current');	

					})
					</script>
		
			<?php } ?>
	
	
	<?php } ?>
	
	
	
	
	
	
	
	
	<!-- For allocation window-->
	
	
	<?php if($this->params['controller'] == 'allocations') { ?>
	
		<script>
			$(document).ready(function(){
			$('ul.tabs li').addClass('current');
	
			//$('.tab-content').addClass('current');
	
		})
		</script>
	
	<?php } ?>
	
	
	<?php if($this->params['action'] == 'pending_forms' || 
				$this->params['action'] == 'allocated_forms' ||
				$this->params['action'] == 'approved_forms' ||
				$this->params['action'] == 'renewal_pending_forms' || 
				$this->params['action'] == 'renewal_allocated_forms' ||
				$this->params['action'] == 'renewal_approved_forms'
				//$this->params['action'] == 'home'
				){?>
	
		<script>
		$(document).ready(function(){
			
				$('#allocate_forms').addClass('current');
				$('#allocate_forms_content').addClass('current');


		})
		</script>
		
	<?php }elseif($this->params['action'] == 'pending_sites' || 
					$this->params['action'] == 'allocated_sites' ||
					$this->params['action'] == 'approved_sites' ||
					$this->params['action'] == 'renewal_pending_sites' || 
					$this->params['action'] == 'renewal_allocated_sites' ||
					$this->params['action'] == 'renewal_approved_sites'){ ?>
	
		<script>
		$(document).ready(function(){
			

				$('#allocate_sites').addClass('current');
				$('#allocate_sites_content').addClass('current');

		})
		</script>
		
	
	<?php }elseif($this->params['action'] == 'ho_pending' || 
					$this->params['action'] == 'ho_allocated'
					){ ?>
	
		<script>
		$(document).ready(function(){
			

				$('#allocate_dy_ama').addClass('current');
				$('#allocate_dy_ama_content').addClass('current');

		})
		</script>
		
	
	<?php }elseif( $this->params['action'] == 'ho_mo_pending' ||
					$this->params['action'] == 'ho_mo_allocated'
					){ ?>
	
		<script>
		$(document).ready(function(){
			

				$('#allocate_mo_smo').addClass('current');
				$('#allocate_mo_smo_content').addClass('current');

		})
		</script>
		
	
	<?php }elseif(	$this->params['action'] == 'ho_jtama_pending' ||
					$this->params['action'] == 'ho_jtama_allocated'
					){ ?>
	
		<script>
		$(document).ready(function(){
			

				$('#allocate_jtama').addClass('current');
				$('#allocate_jtama_content').addClass('current');

		})
		</script>
		
	
	<?php }elseif(	$this->params['action'] == 'ho_ama_pending' ||
					$this->params['action'] == 'ho_ama_allocated'
					){ ?>
	
		<script>
		$(document).ready(function(){
			

				$('#allocate_ama').addClass('current');
				$('#allocate_ama_content').addClass('current');

		})
		</script>
		
	
	<?php }?>
