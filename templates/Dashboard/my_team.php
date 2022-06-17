<?php ?>

<?php if ($_SESSION['division'] == 'BOTH') { ?>
    <?php echo $this->element('my_team/for_dmi'); ?>
<?php } else { ?>
    <?php echo $this->element('my_team/for_lims'); ?>
<?php } ?>