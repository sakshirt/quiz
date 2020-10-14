<div class="container">
  <div class="row">
    <div class="col-12 text-center"> <h2 class="heading"><?php echo ucwords($category_data->category_title); ?></h2><hr></div>

    <?php 
      $category_img_dir = base_url("assets/images/category_image/");
      foreach ($sub_category_data as $category_array) 
      {
        $category_image = $category_array->category_image ? $category_array->category_image : "default.jpg";
        $category_image = $category_img_dir.$category_image;
        ?>

      <div class="col-md-4 col-xl-4 col-sm-6" > 
        <div class="grid">

          <figure class="effect-ming">
            <img src="<?php echo xss_clean($category_image); ?>" alt="img09"/>
            <figcaption>
              <h2><?php echo xss_clean($category_array->category_title);  ?></h2>
              <a href="<?php echo  base_url('category/').$category_array->category_slug ?>"><?php echo lang('view_more') ?></a>
            </figcaption>     
          </figure>
        </div>
      </div>

      <?php } ?>

      <?php if(count($sub_category_data)) { ?>
        <div class="col-12"><hr></div>
      <?php } ?>

  </div>
  <div class="row">
       
    <div class="col-10"></div>
      <div class="col-2 mb-4">
        <form action="" method="GET" id="Quiz_filter_form">
          <div class="form-controll">
            <select class="form-control" id="Quiz_filter" name="most">
              <option  value="">Select One</option>
              <option <?php echo $this->input->get('most') == 'recent' ? 'Selected': ''; ?> value="recent"><?php echo lang('most_recent'); ?></option>
              <option <?php echo $this->input->get('most') == 'liked' ? 'Selected': ''; ?> value="liked"><?php echo lang('most_liked'); ?></option>
              <option <?php echo $this->input->get('most') == 'attended' ? 'Selected': ''; ?> value="attended"><?php echo lang('most_attended'); ?></option>
            </select>
          </div>
        </form>
      </div>
    <?php
      $data['quiz_list_data'] = $quiz_data;
      $this->load->view('quiz_data_list',$data);  
    ?>
  </div>
  <?php echo xss_clean($pagination) ?>
</div>
