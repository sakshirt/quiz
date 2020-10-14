<?php if($blog_list_data) {
   foreach($blog_list_data as $like_post_key => $like_post_value)
   {      
?>
	<div class="col-md-4 col-xl-4 col-sm-6 blog-page">
      <div class="grid">
         <figure class="effect-ming">
            <div class="quiz_icons">
               <a href="/javascript:void(0)" class="icon float-left text-white-im post-view"><i class="fe fe-eye mr-1"></i> 
                 <span class="value"><?php echo xss_clean($like_post_value->view_total_post);?></span>
                </a>
                <a href="javascript:void(0)" class="icon inline-block ml-3 float-right like-quiz text-white-im post-like">
                     <?php $like_or_not = (isset($like_post_value->is_like) && !empty($like_post_value->is_like) ? 'text-success' : 'text-white-im');?>
                     <i class="fav_icon fas fa-heart <?php echo xss_clean($like_or_not);?> "></i> 
                     <?php $total_like = (!empty($like_post_value->total_like) && $like_post_value->total_like > 0 ? $like_post_value->total_like : "");?>
                     <span class="value like-quiz-count"><?php echo xss_clean($total_like);?></span>
                 </a>
            </div>
            <?php 
               $blog_post_image = (isset($like_post_value->post_image) && !empty($like_post_value->post_image) ? base_url('assets/images/blog_image/post_image/'.$like_post_value->post_image) : base_url('assets/images/blog_image/default.jpg'));
            ?>
            <img src="<?php echo xss_clean($blog_post_image);?>" alt="img09"/>
            <figcaption>
               <?php 
                  $title = strlen($like_post_value->post_title) > 10 ? substr($like_post_value->post_title,0,7)."..." : $like_post_value->post_title;
               ?>
              <h2><?php echo xss_clean($title);  ?></h2>
              <a href="<?php echo  base_url('blog-detail/').$like_post_value->post_slug ?>"><?php echo lang('view_more') ?></a>
            </figcaption>
         </figure>
         <div class="text-center"><?php echo xss_clean($like_post_value->post_title);?></div>
          <div class="float-left blog-author">
            <a href="javascript:void(0)"><?php echo xss_clean($like_post_value->first_name ." ". $like_post_value->last_name);?></a>
          </div>
          <div class="float-right blog-author">
            <a href="javascript:void(0)"><?php echo xss_clean($like_post_value->title);?></a>
          </div>
      </div>
   </div>
<?php } } 
	else {
?>
	<div class="col-12 text-center text-danger"> <?php echo lang('no_data_found'); ?></div>
<?php } ?>