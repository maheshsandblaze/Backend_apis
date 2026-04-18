<div class="content-wrapper">
    <section class="content-header"></section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-users"></i> Gallery List</h3>
                        <div class="box-tools pull-right">
                           
                        </div>
                    </div>

                    <div class="row text-center">
                        <?php foreach ($gallery_list as $gallery) { ?>
							<div class="col-lg-3 col-md-6 col-sm-6">
								<div class="card-body-logo">
									<h4><?php echo $gallery['name']; ?></h4>
									 <a href="<?= base_url('admin/staff/image_delete/' . $gallery['id']); ?>" 
															class="btn btn-default btn-xs pull-right"  
															data-toggle="tooltip" 
															title="<?php echo $this->lang->line('delete'); ?>" 
															onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
															<i class="fa fa-remove"></i>
														</a>
									<div class="text-center">
										<div class="card-body-logo-img">
											<img src="<?php echo site_url('uploads/gallery/' . $gallery['category_id'] . '/' . $gallery['image']); ?>" alt="<?php echo $gallery['name']; ?>" width="304" height="236">
										</div>
										<p class="bolds ptt10"><?php echo $gallery['name']; ?></p>
									</div>
								</div>
							</div>
						<?php } ?>

                    </div>

                    <br/>
                </div>
            </div>
        </div>
    </section>
</div>



<!-- Add Image Modal -->
<div id="addCategoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Image</h4>
            </div>
            <div class="modal-body">
               <form action="<?= base_url('admin/staff/add_gallery_image'); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">

                    <div class="form-group">
                        <label for="category_image">Image Name:</label>
                        <input type="text" class=" form-control" name="name" required>
                    </div>
					<div class="form-group">
                        <label for="category_image">Upload Image:</label>
                        <input type="file" class="filestyle form-control" name="category_image" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

