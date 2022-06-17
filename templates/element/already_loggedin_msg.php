<?php ?>

<!-- Show multiple login alert message, Done By Pravin Bhakare 28-09-2020 -->
<!-- Modal: modalAbandonedCart-->
<div class="modal right" id="modalAbandonedCart" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog modal-side modal-bottom-right modal-notify modal-info" role="document" style="width:100%;">
    <!--Content-->
    <div class="modal-content mt-5">
      <!--Header-->
      <div class="modal-header bg-danger text-white pt-2 pb-2">
		<div class="col-md-12"><h4 class="mb-0 fs-18">Alert: You Are Already Logged In</h4></div>
		<!--<div class="col-md-4">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true" class="white-text">&times;</span>
			</button>
		</div>-->
      </div>
      <!--Body-->
      <div class="modal-body">
        <div class="row"> 
          <div class="col-md-12" style="padding-left: 34px; font-size: 17px">
			      You are Already Logged-In from Different Window/Browser.<br> Continuing Here will Logout from Previous Window/Browser.            
          </div>
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer justify-content-center">
        <a class="btn btn-info" id="okbtn">Login</a>
        <a class="btn btn-info" id="cancelbtn" data-dismiss="modal">Cancel</a>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>

<input type="hidden" id="webroot" value="<?php echo $this->request->getAttribute('webroot'); ?>">
<?php
  //created/updated/added on 25-06-2021 for multiple logged in check security updates, by Amol
  if(!empty($already_loggedin_msg)){ 
    echo $this->Html->script('element/already_loggedin_msg');
  }

?>