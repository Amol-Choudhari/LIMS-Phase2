 <!-- Main content -->
 <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">

          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><span class="percent"><?php echo $lab_status_count['total_registered']; ?></h3>

                <h6><b>Samples Registered</b></h6>
              </div>
              <div class="icon">
                <i class="fa fa fa-copy"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><span class="percent"><?php echo $lab_status_count['total_allocated']; ?></h3>

                <h6><b>Allocated for Test</b></h6>
              </div>
              <div class="icon">
                <i class="fa fa-print"></i>
              </div>
             </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><span class="percent"><?php echo $lab_status_count['results_approved']; ?></h3>

                <h6><b>Test Results Approved</b></h6>
              </div>
              <div class="icon">
                <i class="fa fa-flask"></i>
              </div>
             </div>
          </div>
          <!-- ./col -->
		   <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><span class="percent"><?php echo $lab_status_count['report_finalized']; ?></span></h3>

                <h6><b>Reports Finalized</b></h6>
              </div>
              <div class="icon">
                <i class="fa fa-edit"></i>
              </div>
             </div>
          </div>
          <!-- ./col -->
        </div>

	</div>
	</section>
</div>
