<?php
if (!defined('ABSPATH')) exit;

function spike_render_medical_step($diagram){ ?>
  <section data-step="2">
    <h3 class="section-title">Medical History</h3>

    <div class="grid">
      <?php
      echo spike_input('mh_reason','Reason for visit TODAY','text',false);

      // Pain description (check all) + Other
      $pain=['Dull Ache','Sharp','Shooting','Throbbing','Numbness','Burning','Tingling'];
      echo '<div class="field"><label>How would you describe your pain? (check all)</label><div class="checks">';
      foreach($pain as $p){ echo '<label><input type="checkbox" name="mh_pain_desc[]" value="'.$p.'"> '.$p.'</label>'; }
      echo '</div><label>Other: <input type="text" name="mh_pain_desc_other"></label></div>';

      echo spike_select('mh_travel','Does your pain travel or shoot anywhere?',[''=>'','No'=>'No','Yes'=>'Yes'],false);
      echo spike_input('mh_travel_where','If yes, where?','text',false);
      echo spike_input('mh_duration','How long have you had this pain?','text',false);
      echo spike_input('mh_onset','How did it start?','text',false);
      echo spike_input('mh_aggravates','What aggravates your condition?','text',false);
      echo spike_input('mh_helps','What helps your condition?','text',false);
      echo spike_input('mh_frequency','How often do you have this pain?','text',false);
      echo spike_input('mh_constant','Is the pain constant or come and go?','text',false);

      // Interferes with
      echo '<div class="field"><label>Is this condition interfering with:</label><div class="checks">';
      foreach(['Work','Sleep','Daily Routine'] as $o){ echo '<label><input type="checkbox" name="mh_interferes[]" value="'.$o.'"> '.$o.'</label>'; }
      echo '</div><label>Other: <input type="text" name="mh_interferes_other"></label></div>';

      // Group: Seen other provider? -> If yes, who?
      echo '<div class="field"><label>Have you seen any other provider for this condition?</label>';
      echo '<select id="mh_seen_provider" name="mh_seen_provider"><option value=""></option><option>No</option><option>Yes</option></select>';
      echo '<label style="margin-top:6px" for="mh_seen_who">If yes, who?</label><input type="text" id="mh_seen_who" name="mh_seen_who"></div>';

      // Group: Any diagnostic testing? -> If yes, when?
      echo '<div class="field"><label>Any diagnostic testing done (X-ray, MRI, CT Scan, Ultrasound)?</label>';
      echo '<input type="text" id="mh_testing" name="mh_testing">';
      echo '<label style="margin-top:6px" for="mh_testing_when">If yes, when?</label><input type="text" id="mh_testing_when" name="mh_testing_when"></div>';
      ?>
    </div>

    <?php
    // Conditions checklist + Other
    $conds=['AIDS/HIV','Allergy Shots','Anemia','Arthritis','Asthma','Bleeding Disorder','Cancer','Diabetes','Emphysema','Epilepsy','Gout','Heart Disease',
            'Hepatitis','Herniated Disc','Migraines','Multiple Sclerosis','Mumps','Pacemaker',"Parkinsons' Disease",'Pinched Nerve','Pneumonia','Polio',
            'Chiropractic Care','Rheumatoid Arthritis','Stroke','Thyroid Problems','Tumors/Growths'];
    echo '<div class="field"><label>Check all that apply</label><div class="checks">';
    foreach($conds as $c){ echo '<label><input type="checkbox" name="mh_conditions[]" value="'.$c.'"> '.$c.'</label>'; }
    echo '</div><label>Other: <input type="text" name="mh_conditions_other"></label></div>';
    ?>

    <div class="grid">
      <?php
      // Exercise
      echo '<div class="field"><label>Exercise (check all that apply)</label><div class="checks">';
      foreach(['None','Moderate','Daily','Heavy'] as $e){ echo '<label><input type="checkbox" name="mh_exercise[]" value="'.$e.'"> '.$e.'</label>'; }
      echo '</div></div>';

      // Work activity
      echo '<div class="field"><label>Work Activity (check all that apply)</label><div class="checks">';
      foreach(['Sitting','Standing','Light Labor','Heavy Labor'] as $w){ echo '<label><input type="checkbox" name="mh_work_activity[]" value="'.$w.'"> '.$w.'</label>'; }
      echo '</div></div>';

      // Habits (checkbox + textbox each)
      echo '<div class="field"><label>Habits</label><div class="checks">';
      echo '<label><input type="checkbox" name="mh_habit_smoking" value="Yes"> Smoking – Packs/Day: <input type="text" name="mh_habit_smoking_amt" style="width:6em"></label>';
      echo '<label><input type="checkbox" name="mh_habit_alcohol" value="Yes"> Alcohol – Drinks/Week: <input type="text" name="mh_habit_alcohol_amt" style="width:6em"></label>';
      echo '<label><input type="checkbox" name="mh_habit_caffeine" value="Yes"> Coffee/Caffeine – Cups/Day: <input type="text" name="mh_habit_caffeine_amt" style="width:6em"></label>';
      echo '</div></div>';

      // Family / Pregnancy
      echo spike_select('mh_pregnant','Family — Are you Pregnant?',[''=>'','No'=>'No','Yes'=>'Yes'],false);
      ?>
    </div>

    <div class="field"><label>Injuries/Surgeries (Falls, Head Injuries, Broken Bones, Dislocations, Surgeries)</label><textarea name="mh_injuries" rows="3"></textarea></div>
    <div class="field"><label>Medications</label><textarea name="mh_meds" rows="2"></textarea></div>
    <div class="field"><label>Allergies</label><textarea name="mh_allergies" rows="2"></textarea></div>

    <!-- Pain diagram (unchanged) -->
    <div class="field">
      <label>Pain Diagram (tap up to 10 spots). Use Clear to redo.</label>
      <div class="diagram"><div class="toggle"><button class="btn small secondary" data-clear type="button">Clear</button></div>
        <div class="diagram-box"><img id="spike-diagram" src="<?php echo esc_url($diagram); ?>" alt="Pain diagram"><canvas id="spike-layer"></canvas></div>
        <input type="hidden" name="pain_points" id="spike_points" value="[]">
      </div>
    </div>

    <div class="actions"><button type="button" class="btn secondary" data-prev>Back</button><button type="button" class="btn" data-next>Next</button></div>
  </section>
<?php }
