<?php
if (!defined('ABSPATH')) exit;

/** Returns the full consent HTML used on Step 3 and in the PDF. */
function spike_consent_html(){
  return '
  <div class="consent-body">
    <h4>Informed Consent</h4>
    <p>Please read this entire document before signing. It is important that you understand the information contained in this document. Please ask questions before you sign if anything is unclear.</p>

    <h5>The nature of the chiropractic adjustment</h5>
    <p>The primary treatment used by a Doctor of Chiropractic is spinal manipulative therapy; I may use my hands or a mechanical instrument to move joints in a way that seeks to restore proper motion. You may hear or feel a "pop" or "click" and may feel temporary soreness similar to when you "crack" your knuckles.</p>

    <h5>Analysis / Examination / Treatment</h5>
    <ul>
      <li>Palpation/range of motion and orthopedic/neurological/postural testing</li>
      <li>Spinal manipulative therapy</li>
      <li>Physiotherapy / massage therapy / decompression therapy</li>
    </ul>

    <h5>The material risks inherent in chiropractic adjustment</h5>
    <p>As with any healthcare procedure, complications are possible though uncommon. These include but are not limited to: fractures, disc injury, dislocations, muscle strain, cervical myelopathy, costovertebral sprains, and burns. Some patients may feel soreness or stiffness following the first few days of treatment. It is important to inform the doctor about conditions such as osteoporosis, severe arthritis, or other health issues.</p>

    <h5>The availability and nature of other treatment options</h5>
    <ul>
      <li>Self-administered care: rest, ice/heat, exercise</li>
      <li>Medical care and prescription drugs (anti-inflammatory, muscle relaxants, pain relievers)</li>
      <li>Hospitalization / surgery</li>
    </ul>

    <h5>The risks and dangers of remaining untreated</h5>
    <p>Remaining untreated may allow adhesions to form and reduce mobility, which may set up a pain reaction that further reduces mobility. Over time this may complicate treatment, making it more difficult and less effective.</p>

    <h5>Notice of Privacy Practices</h5>
    <p>I understand the “Notice of Privacy Practices for Protected Health Information,” describing how my medical information may be used and disclosed, and how I can get access to this information. (A copy can be provided to me at any time.)</p>

    <h5>Minor Consent</h5>
    <p>I hereby authorize PHI Manager, together with whomever they may designate, to treat the minor child/children whose name(s) appear above. When necessary, chiropractic care (including X-rays and appropriate evaluations) may be administered if, in the treating judgment, care is necessary. I acknowledge that I have legal authority to provide such written consent.</p>

    <p><strong>DO NOT SIGN UNTIL YOU HAVE READ AND UNDERSTOOD THE ABOVE</strong></p>
  </div>';
}
