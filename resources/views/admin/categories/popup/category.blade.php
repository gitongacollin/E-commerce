<div class="modal fade" id="category-delete-show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center">Delete Confirmation</h4>
			</div>
			@foreach($categories as $category)
			<form action="{{ url('admin/delete-category/'. $category->id) }}"method="post">
				@method('post')
				@csrf
				<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<p class="text-center">
							Are you sure you want to delete this Category?
						</p>
						<input type="hidden" name="category_id" id="cat_id" value="">
						
					</div>
				</div>
				
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
					<button class="btn btn-warning btn-delete-category" type="button">Delete</button>
				</div>
			</form>
			@endforeach
			
		</div>
		
	</div>
	
</div>