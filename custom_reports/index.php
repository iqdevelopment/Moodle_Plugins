<?php
    require_once("../config.php");
    require_once("lib.php");
   // require_once($CFG->dirroot.'/user/profile/lib.php');
    
    global $DB,$PAGE, $CFG,$USER;

    $PAGE->set_title($SITE->fullname);
  $PAGE->set_heading($SITE->fullname);
  if (!isloggedin() or isguestuser()) {
    // Do not use require_login() here because we might have already used require_login($course).
    redirect(get_login_url());
}
    echo $OUTPUT->header();
    echo "<script> var userEmail = '".$USER->email."'</script>";

  echo "<div id='wizard' class='wizard'>Pokud vidíte tento text, prosím zapněte Javascript ve svém prohlížeči!</div>";

  echo includesFiles();
echo $OUTPUT->footer();

?>