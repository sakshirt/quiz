<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid pt-5 body_background">
   <div class="container">
      <div class="row">
         <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="card card-primary">
               <div class="card-header">
                  <h4>Login</h4>
               </div>
               <div class="card-body">
                  <?php echo form_open('', array('class'=>'form-signin formlogin')); ?>
                  <div class="form-group">
                     <?php echo form_label(lang('mobile'), 'username_email'); ?> 
                     <?php echo form_input(array('name'=>'mobile', 'id'=>'mobile', 'class'=>'form-control', 'placeholder'=>lang('mobile'), 'maxlength'=>256)); ?>
                     <span class="small text-danger"> <?php echo strip_tags(form_error('mobile')); ?> </span>
                  </div>
                  <div class="form-group">
                     <div class="d-block">
                        <?php echo form_label(lang('password'), 'password'); ?>
                        <div class="float-right">
                           <a href="<?php echo base_url('user/forgot'); ?>" class="text-small"><?php echo lang('user_forgot_password'); ?></a>
                        </div>
                     </div>
                     <?php echo form_password(array('name'=>'password', 'id'=>'password', 'class'=>'form-control', 'placeholder'=>lang('password'), 'maxlength'=>72, 'autocomplete'=>'off')); ?>
                     <span class="small text-danger"> <?php echo strip_tags(form_error('password')); ?> </span>
                  </div>

                  <div class="form-group">
                     <?php echo form_submit(array('name'=>'submit', 'class'=>'btn btn-primary btn-lg btn-block'), lang('core_button_login')); ?>
                  </div>

                  <div class="form-group">
                     <input type="hidden" id="fb_app_id" value="<?php echo $this->settings->facebook_app_id;?>">
                     <?php 
                        $facebook_permissions = $this->config->item('facebook_permissions') ? $this->config->item('facebook_permissions') : array();
                      ?>
                     <input type="hidden" id="fb__permissions" value="<?php echo implode(',',$facebook_permissions); ?>">

                     <div class="row">
                        <?php if(!empty($this->settings->facebook_app_id) && !empty($this->settings->facebook_app_secret)) {  ?>
                           <div class="col-12 mb-4">
                              <a href="javascript:void(0)" id="login" class="btn btn-lg btn-primary bg-blue btn-block " role="button"><i class="fab fa-facebook-square mr-2"></i>Facebook</a>
                           </div>
                        <?php } if(!empty($this->settings->google_key) && !empty($this->settings->google_secret)) { ?>
                           <div class="col-12">
                              <a href="<?php echo $login_url; ?>" class="btn btn-lg btn-danger btn-block" role="button"><i class="fab fa-google-plus-square mr-2"></i> Google+</a>
                           </div>
                        <?php } ?>
                     </div>  
                  </div>
                     <p><a href="<?php echo base_url('user/register'); ?>"><?php echo lang('user_link_register_account'); ?></a></p>
                  <?php echo form_close(); ?> 
               </div>
            </div>
         </div>
      </div>
   </div>
</div>