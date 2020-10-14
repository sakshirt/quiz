<div class="container">
	<div class="row">
    	<div class="col-md-12">
      		<div class="card mt-5">
      			<div class="card-body">
      				<div class="text-wrap p-lg-6"> 
			            <h1 class="text-primary text-center"><?php echo $quiz_data->title; ?></h1>
			            <hr>
			        </div>
			        <div class="row">
				        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3"> 
				        	<div class="detail-left">
				        		<?php  
				        			$quiz_title = strlen($quiz_data->title) > 40 ? substr($quiz_data->title,0,40)."..." : $quiz_data->title;
				        		?>
				        		<div class="detail_quiz_title">Quiz Title: <?php echo xss_clean($quiz_title); ?></div>
				        		<div class="detail_quiz_title">No. Of Questions: <?php echo xss_clean($quiz_data->number_questions); ?> </div>
				        		<div class="detail_quiz_title">Duration: <?php echo xss_clean($quiz_data->duration_min); ?> </div>
				        		<div class="detail_quiz_title">Like The Quiz: 
				        			<a class="btn btn-neutral btn-fill like-quiz" data-toggle="tooltip"  title="<?php echo lang('like'); ?>">
				        			<?php $like_or_not = (isset($quiz_data->like_id) && !empty($quiz_data->like_id) ? 'text-success' : 'text-muted');?>
				        			<i class="fav_icon fas fa-heart <?php echo xss_clean($like_or_not);?>" data-quiz_id="<?php echo xss_clean($quiz_data->id);?>"></i>
				        			</a>
				        		</div>
				        		<?php $quiz_price = $quiz_data->price > 0 ? $quiz_data->price : "Free"; ?>  
				        		<div class="detail_quiz_title">Quiz Price: <?php echo $quiz_price; ?></div> 
				        	</div>
				        </div>
				        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-9"> 
				        	<p class="text-justify"><?php echo xss_clean($quiz_data->description);?></p>
				        </div>
				    </div>
				    <hr>
				    <div class="row">
				    	<?php if(empty($comments_exist_quizid_userid)) 
                     		{
                  		?>
					    	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-6">
					        	<?php echo form_open('rating', array('role'=>'form',)); ?>
					        		<input type="hidden" name="quizid" class="quizid" value="<?php echo $quiz_data->id?>">
					        		<textarea class="form-control save-heading" name="ratingcontent" placeholder="Wrtie your comment here"></textarea>
					        		<section class='rating-widget'>
									  <!-- Rating Stars Box -->
									  <div class='rating-stars'>
									    <ul id='stars'>
									      <li class='star' data-toggle="tooltip" data-placement="bottom" title="Poor" data-value='1'>
									        <i class='fa fa-star fa-fw'></i>
									      </li>
									      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Fair' data-value='2'>
									        <i class='fa fa-star fa-fw'></i>
									      </li>
									      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Good' data-value='3'>
									        <i class='fa fa-star fa-fw'></i>
									      </li>
									      <li class='star' data-toggle="tooltip" data-placement="bottom" title='Excellent' data-value='4'>
									        <i class='fa fa-star fa-fw'></i>
									      </li>
									      <li class='star' data-toggle="tooltip" data-placement="bottom" title='WOW!!!' data-value='5'>
									        <i class='fa fa-star fa-fw'></i>
									      </li>
									    </ul>
									  </div>
									  <span class="small text-danger"> <?php echo strip_tags(form_error('title')); ?> </span>
									  <div class='success-box'>
									    <div class='text-message'></div>
									    <div class='clearfix'></div>
									  </div>
									</section>
	                                <input type="hidden" class="rate" name="reviewstar" value="">
	                                <input type="submit" name="save" value="Save" class="btn btn-primary btn-lg">
					        	<?php form_close();?>
					        </div>
				        <?php } ?> 
				        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
				        	<div class="border border-secondary">
				        		<?php 
                              		$average_rating = (isset($average) && !empty($average) ? round($average) : 0); 
                           		?>
				        		<div class="average-text">Average Rating</div>
				        		<div class="average-star">
				        			<?php if($average_rating >= 1) { ?>
				        				<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
				        			<?php } else { ?>
				        				<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
                                 	<?php }	if($average_rating >= 2) { ?>
				        				<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                                 	<?php } else { ?>   
                                    	<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
                                 	<?php } if($average_rating >= 3) { ?>
                                 		<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
	                                 <?php } else { ?>   
	                                    <span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
	                                <?php } if($average_rating >= 4) { ?>      
	                                    <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
	                                <?php } else { ?>   
	                                    <span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
	                                <?php } if($average_rating >= 5) { ?>      
	                                    <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
	                                <?php } else { ?>   
	                                    <span><i class="far fa-star empty-star" aria-hidden="true"></i></span>   
	                                <?php  } ?>
				        			<span>(<?php echo $average_rating;?>)</span>
				        		</div>
				        	</div>
				        </div> 
				        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
				        	<div class="share">
                              <span class="pr-3">Share</span>
                              <span class="pr-3"><a href="//www.facebook.com/sharer.php?u=<?php echo base_url('quiz-detail/').$quiz_data->id; ?>"  target="_blank"><i class="fab fa-facebook-f"></i></a></span>
                              <span class="pr-3"><a href="//twitter.com/share?text=<?php echo str_replace(' ', '+', $quiz_data->title); ?>&url=<?php echo base_url('quiz-detail/').$quiz_data->id; ?>" target="_blank"><i class="fab fa-twitter"></i></a></span>
                              <span class="pr-3"><a href="tg://msg?text=<?php echo str_replace(' ', '+', $quiz_data->title); ?> on <?php echo base_url('quiz-detail/').$quiz_data->id; ?>" target="_blank"><i class="fab fa-telegram-plane"></i></a></span>
                              <span class="pr-3"><a href="//web.whatsapp.com/send?text= <?php  echo str_replace(' ', '+', $quiz_data->title); ?> on <?php  echo base_url('quiz-detail/').$quiz_data->id; ?>" target="_blank"><i class="fab fa-whatsapp"></i></a></span>
                            </div>  
				        </div>
				    </div>
				    <hr>
				    <?php   
                     $count = 0;
                     foreach($quiz_comments as $comment_key => $comment_value)
                     {
                      	$count ++;
               		?>
					    <div class="comment-list">
						    <div class="row">
						    	<div class="col-md-1">
						    		<?php $userimage = (isset($comment_value->image) && !empty($comment_value->image) ? base_url('/assets/images/user_image/'.$comment_value->image) : base_url('assets/images/user_image/avatar-1.png'));?>
                           			<div class="user-image"><img src="<?php echo $userimage;?>"></div>
						    	</div>
						    	<div class="col-md-2">
						    		<div class="user-name"><?php echo $comment_value->first_name. ' ' .$comment_value->last_name;?></div>
						    		<div class="comment-date"><?php echo date('d.m.Y H:i', strtotime($comment_value->added));?></div>
						    	</div>
						    	<div class="col-lg-3">
						    		<div class="averate-number-main">
						    			<div class="detail-average-star">
						    				<?php if($comment_value->rating >= 1) { ?>
                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                                 			<?php } else { ?> 
                                 				<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
                                 			<?php } if($comment_value->rating >= 2) {?>	
                                 				<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                                 			<?php } else { ?>   
                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
                                    		<?php } if($comment_value->rating >= 3) {  ?>   
                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                                 			<?php } else { ?>   
                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>	
                                    		<?php } if($comment_value->rating >= 4) { ?>
                                    			<span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
                                 			<?php } else { ?>   
                                    			<span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
                                    		<?php } if($comment_value->rating >= 5) { ?>
			                                    <span><i class="fa fa-star fill-star" aria-hidden="true"></i></span>
			                                <?php } else { ?>   
			                                    <span><i class="far fa-star empty-star" aria-hidden="true"></i></span>
			                                <?php } ?>	
						    			</div>
						    		</div>
						    	</div>
						    	<div class="col-lg-6">
						    		<div class="comment-text"><p><?php echo $comment_value->review_content;?></p></div>
						    		<?php
			                            if(get_user_review_like($comment_value->id))
			                            {
			                            	$liked_or_not = 'review-like';
			                            } 
			                            else
			                            {
			                                $liked_or_not = 'review-not-visit';
			                            }
			                        ?>
						    		<div class="evet thumbs-up ">
						    			<i class="fa fa-thumbs-up <?php echo $liked_or_not;?>" id="change-color_<?php echo $comment_value->id; ?>" aria-hidden="true" data-review_id="<?php echo $comment_value->id;?>"></i>
						    			<span class="total-likes"></span>
						    		</div>
						    	</div>
						    </div>
						</div>
					<?php } ?>	    
      			</div>
    		</div>
    	</div>
    </div>
</div>