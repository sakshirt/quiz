<div class="container">
  <div class="page-header">
    <h1 class="page-title">
      <?php echo lang('read_instructions_carefully'); ?>
    </h1>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="text-wrap p-lg-6">
            <?php echo get_admin_setting('general_instructions'); ?>
          </div>
        </div>
        <?php 
          if ($quiz_data->quiz_instruction) {
        ?>
          <div class="card-body">
            <div class="text-wrap p-lg-6">
              <h1 class="text-primary"><?php echo lang('front_quiz_instruction'); ?></h1>
              <hr>
              <?php 
                $lang_id = get_language_data_by_language($this->session->userdata('language'));
                $translate_instructions = get_translated_column_value($lang_id,'quizes',$quiz_data->id,'quiz_instruction');
                $translate_instructions = $translate_instructions ? $translate_instructions : $quiz_data->quiz_instruction;
                  echo xss_clean($translate_instructions); 
              ?>
            </div>
          </div>
        <?php  } ?>
        <div class="card-footer">
          <div class="card-options">
            <a href="<?php echo base_url("start-test/$quiz_id") ?>" class="btn btn-primary btn-lg btn-block"><?php echo lang('start_quiz'); ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
