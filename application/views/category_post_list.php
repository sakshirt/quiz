<div class="container">
	<div class="row">
	    <div class="col-12 text-center"> <h2 class="heading"><?php echo ucwords($blog_category_data->title); ?></h2> <hr></div>
	    <?php if($post_data_array) 
	    	  {
	    	  	foreach($post_data_array as $blog_key => $blog_value)
	    		{					
	    ?>
	    			<div class="col-md-4 col-xl-4 col-sm-6 blog-page">
	    				<div class="grid">
				          <figure class="effect-ming">
				          	<?php 
				          		$blog_post_image = (isset($blog_value->post_image) && !empty($blog_value->post_image) ? base_url('assets/images/blog_image/post_image/'.$blog_value->post_image) : base_url('assets/images/blog_image/default.jpg'));

				          	?>
				            <img src="<?php echo xss_clean($blog_post_image); ?>" alt="img09"/>
				            <figcaption>
				              <h2><?php echo xss_clean($blog_value->post_title);  ?></h2>
				              <a href="<?php echo  base_url('blog/post/').$blog_value->post_slug ?>"><?php echo lang('view_more') ?></a>
				            </figcaption>     
				          </figure>
				          <div class="text-center"><?php echo xss_clean($blog_value->post_title);?></div>
				          <div class="float-left blog-author">
				          	<a href="javascript:void(0)"><?php echo xss_clean($blog_value->first_name ." ". $blog_value->last_name);?></a>
				          </div>
				          <div class="float-right blog-author">
				          	<a href="javascript:void(0)"><?php echo xss_clean($blog_value->title);?></a>
				          </div>
				        </div>
	    			</div>		
	    <?php 
	    		}
	    	}
	    ?>	

	    <div class="col-12"><?php echo xss_clean($pagination); ?></div>	
	</div>
</div>