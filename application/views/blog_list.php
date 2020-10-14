<div class="container">
	<div class="row">
	    <div class="col-12 text-center"> <h2 class="heading"><?php echo ucwords($title); ?></h2> <hr></div>
	    <div class="col-md-9 col-xl-9 col-sm-6">
	    	<div class="row">
	    		<?php 
                   $data['blog_list_data'] = $blog_post_data;
                   $this->load->view('common_blog_list',$data);
                ?>
	    	</div>
	    	<?php echo xss_clean($pagination) ?>
	    </div>
	    <div class="col-md-3 col-xl-3 col-sm-12">
	    	
	    	<div class="list-group">
	    		<?php $active = $category_slug == 'all' OR $category_slug =='ALL' ? ' active' : '' ?>
	    		<a href="<?php echo base_url('blog/list/all');?>" class="list-group-item list-group-item-action list-group-item-secondary <?php echo $active; ?>">
			       	<h4 class="m-0"> <?php echo lang("admin_blog_category_list");?></h4>
			    </a> 

		    	<?php 
		    	if($blog_category)
	    		{
	    			foreach($blog_category as $blog_category_key => $blog_category_value)
	    			{ 
	    				$active = $category_slug == $blog_category_value->slug ? ' active' : '' ?>

	    				<a href="<?php echo base_url('blog/list/'.$blog_category_value->slug);?>" class="list-group-item list-group-item-action <?php echo $active; ?>">
			       			<i class="fas fa-angle-right"></i> <?php echo $blog_category_value->title;?>
			    		</a> <?php 
	    			}
	    		} ?>	
	    	</div>

	    </div>
	</div>
	    
</div>