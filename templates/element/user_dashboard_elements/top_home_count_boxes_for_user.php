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
                <h3><span class="percent"><?php echo $user_status_count['overall_samples']; ?></h3>

                <p>Over All Samples</p>
              </div>
              <div class="icon">
                <i class="fa fa fa-copy"></i>
              </div>
             </div>
          </div>
		  
		  <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><span class="percent"><?php echo $user_status_count['processed_samples']; ?></h3>

                <p>Processed Samples</p>
              </div>
              <div class="icon">
                <i class="fa fa-flask"></i>
              </div>
             </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><span class="percent"><?php echo $user_status_count['total_allocated']; ?></h3>

                <p>Allocated for Test</p>
              </div>
              <div class="icon">
                <i class="fa fa-print"></i>
              </div>
			</div>
          </div>
          
          <!-- ./col -->
		   <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><span class="percent"><?php echo $user_status_count['report_finalized']; ?></span></h3>

                <p>Reports Generated</p>
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
