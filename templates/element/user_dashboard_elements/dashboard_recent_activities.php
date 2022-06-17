<?php echo $this->Html->css("element/user_dashboard_elements/dashboard_recent_activities"); ?>
<div id="vertimeline">
  <div class="container">

  <div class="rightbox">
    <div class="rb-container">

      <ul class="rb">
	  <h6><b>My Recent Activities</b></h6>

	  <?php foreach($recent_activities as $eachActivity){ ?>
        <li class="rb-item" ng-repeat="itembx">
          <div class="item-title">
			Date: <span class="timestamp"><?php echo $eachActivity['date']; ?></span>
			Sample Code: <span class="timestamp"><?php echo $eachActivity['sample_code']; ?></span><br>
			<b>Activity: <?php echo $eachActivity['activity']; ?></b></div>
        </li>
	  <?php } ?>
      </ul>

    </div>
  </div>
</div>
</div>
