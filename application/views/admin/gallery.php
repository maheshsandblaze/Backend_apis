<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info"><div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> Gallery Category</h3>
                        <div class="box-tools pull-right">
                            
							 <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCategoryModal"><i class="fa fa-plus"></i> Add Category</button>
                            <button onclick="window.history.back(); " class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back</button>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <div > <div class="download_label"><?php echo $this->lang->line('vehicle_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th>ID</th>
										<th>Category Name</th>
										<th>Created At</th>
                                        <th class="text-right noExport" width="10%"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php foreach ($g_categories as $category): ?>
									   <tr>
										   <td><?= $category['id']; ?></td>
										   <td><?= htmlspecialchars($category['category_name']); ?></td>
										   <td><?= $category['created_at']; ?></td>
										   <td class="mailbox-date pull-right no-print white-space-nowrap">
                                                        
                                                        <a href="<?= base_url('admin/staff/gallery_list/' . $category['id']); ?>" 
															class="btn btn-default btn-xs" 
															data-toggle="tooltip" 
															title="<?php echo $this->lang->line('view'); ?>">
															<i class="fa fa-reorder"></i>
														</a>
														
                                                        <a class="btn btn-default btn-xs editCategory" data-id="<?php echo $category['id']; ?>"  
															data-name="<?php echo $category['category_name']; ?>"  
															data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
															<i class="fa fa-pencil"></i>
														</a>

                                                       <a href="<?= base_url('admin/staff/g_category_delete/' . $category['id']); ?>" 
															class="btn btn-default btn-xs"  
															data-toggle="tooltip" 
															title="<?php echo $this->lang->line('delete'); ?>" 
															onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
															<i class="fa fa-remove"></i>
														</a>

                                                    
                                            </td>
									   </tr>
								   <?php endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>




<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Category</h4>
            </div>
            <div class="modal-body">
               <form action="<?= base_url('admin/staff/gallery'); ?>" method="post">
					<div class="form-group">
						<label for="category_name">Category Name:</label>
						<input type="text" class="form-control" name="category_name" id="category_name" required>
					</div>
					<button type="submit" class="btn btn-primary">Add Category</button>
				</form>

            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Category</h4>
            </div>
            <form id="editCategoryForm" action="<?php echo base_url('admin/staff/g_category_update'); ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
	$(document).on('click', '.editCategory', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');

    $('#editCategoryId').val(id);
    $('#editCategoryName').val(name);
    $('#editCategoryModal').modal('show');
});

</script>


