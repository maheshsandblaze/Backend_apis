<style>
.bg-theme {background-color: #9854cb;}

.info-boxxx-content {
    padding: 20px 10px;
    margin-left: 65px;
}

</style>

<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>


<?php
$role    = $this->customlib->getStaffRole();
$role_id = json_decode($role)->id;
?>

<?php if ($role_id != 8) { ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="">

    <div class="box border0">
    <div class="box-header with-border">
        <h3 class="box-title">Select Module</h3>
    </div>
	                <div class="row">
						<?php foreach ($menu_data as $side_list_value) { ?>
						<div class="col-sm-6 col-md-3"> 
								<div class="info-box">
									<a href="<?php echo site_url().$side_list_value['url_path'];?>">
										<span class="bg-theme info-box-icon">
											<img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/') . $side_list_value['icon_image']; ?>">
										</span>
										<div class="info-boxxx-content">
											<span class="info-box-text"> <?php echo $this->lang->line($side_list_value['lang_key']); ?></span>
											
										</div>
									</a>
								</div>
						</div>
						<?php } ?>
					</div>
</div>


          
			</div>


        </section>
    </div>

<?php } ?>

<!--- Fee agent Dashboard Ends --->