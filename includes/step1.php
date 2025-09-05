<?php
if (!defined('ABSPATH')) exit;

function spike_render_step1(){ ?>
  <section data-step="1">
    <h3 class="section-title">Patient Information</h3>
    <div class="grid">
      <?php
      echo spike_input('first_name','First Name','text',true);
      echo spike_input('last_name','Last Name','text',true);
      echo spike_input('middle_initial','M.I.','text',false,'maxlength="1"');
      echo spike_input('dob','DOB','date',false);
      echo spike_input('address1','Address','text',true);
      echo spike_input('address2','Address Line 2','text',false);
      echo spike_input('city','City','text',true);
      echo spike_state_select('MI'); // required, default MI
      echo spike_input('zip','Zip','text',true);
      echo spike_input('primary_phone','Primary Phone','tel',true);
      echo spike_input('secondary_phone','Secondary Phone','tel',false);
      echo spike_select('sex','Sex',spike_opts_sex(),false);
      echo spike_input('email','Email','email',true);
      echo spike_select('race','Race',spike_opts_race(),false);
      echo spike_select('ethnicity','Ethnicity',spike_opts_ethnicity(),false);
      echo spike_select('preferred_language','Preferred Language',spike_opts_language(),false,'','English',false);
      echo spike_select('relationship_status','Relationship Status',spike_opts_relationship(),false);
      echo spike_select('heard_about','How did you hear about our office?',spike_opts_heard_about(),false);
      ?>
    </div>

    <h3 class="section-title">Employment Status</h3>
    <div class="grid">
      <?php
      echo spike_select('employment_status','Employment Status',spike_opts_employment(),false);
      echo spike_input('occupation','Occupation','text',false);
      echo spike_input('employer_school','Employer/School','text',false);
      echo spike_input('employer_phone','Employer Phone','tel',false);
      echo spike_input('employer_address','Employer Address (Street)','text',false);
      echo spike_input('employer_city','Employer City','text',false);
      echo spike_input('employer_zip','Employer Zip','text',false);
      ?>
    </div>

    <h3 class="section-title">Emergency Contact</h3>
    <div class="grid">
      <?php
      echo spike_input('emergency_name','Emergency Contact Name','text',false);
      echo spike_input('emergency_relationship','Relationship','text',false);
      echo spike_input('emergency_phone','Phone','tel',false);
      ?>
    </div>

    <h3 class="section-title">Insurance Information</h3>
    <div class="grid">
      <?php
      echo spike_input('primary_insurance','Primary Insurance','text',false);
      echo spike_input('policy_number','Policy #','text',false);
      echo spike_input('group_number','Group #','text',false);
      echo spike_input('subscriber_name','Subscriber Name','text',false);
      echo spike_input('subscriber_relationship','Subscriber Relationship','text',false);
      echo spike_input('subscriber_dob','Subscriber DOB','date',false);
      echo spike_input('secondary_insurance','Secondary Insurance','text',false);
      echo spike_input('secondary_policy_number','Policy # (Secondary)','text',false);
      echo spike_input('secondary_group_number','Group # (Secondary)','text',false);
      echo spike_input('secondary_subscriber_name','Subscriber Name (Secondary)','text',false);
      echo spike_input('secondary_subscriber_relationship','Subscriber Relationship (Secondary)','text',false);
      echo spike_input('secondary_subscriber_dob','Subscriber DOB (Secondary)','date',false);
      ?>
    </div>

    <h3 class="section-title">Accident Information</h3>
    <div class="grid">
      <?php
      echo spike_select('accident_now','Are you here today because of an accident?',[''=>'','No'=>'No','Yes'=>'Yes'],false,'',null,false);
      echo spike_input('accident_date','Date of Accident','date',false);
      echo spike_select('accident_type','Type of Accident',spike_opts_accident_type(),false);
      echo spike_select('accident_reported_to','To whom did you report your accident?',spike_opts_accident_reported_to(),false);
      ?>
    </div>

    <div class="actions">
      <button type="button" class="btn secondary" data-prev disabled>Back</button>
      <button type="button" class="btn" data-next>Next</button>
    </div>
  </section>
<?php }
