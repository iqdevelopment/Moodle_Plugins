<?php
//require_once('../../../../config.php');
//require_once('../../lib.php');
defined('MOODLE_INTERNAL') || die;
       global $DB,$CFG;
       //require($CFG->wwwroot.'/local/bbb_attendance/lib.php');

class Original_attendance{

    public $courseId;
    public $bbbId;

    public  function __construct(int $courseId, int  $bbbId)
    {
        $this->courseId = $courseId;
        $this->bbbId = $bbbId;

    }


    public function getInitialData()
    {
        global $DB;
        $course = $DB->get_record_sql('select * from {course} WHERE id = '.$this->courseId.'');
        $this->gradeInstanceId =  $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$course->id." AND itemname = 'dochazka'");
        $this->course = $course;
         //nabrani uzivatelu
        $this->students = get_enrolled_students($course->id);
        $this->lectors = get_enrolled_others($course->id);
        //konec nabrani uzivatelu
        $lector_sql = '('.implode(',',$this->lectors).')';
        $this->startTime = $DB->get_field_sql('
            SELECT min(timestamp) 
            FROM {bbb_attendance} 
            INNER JOIN {bbb_attendance_users} 
            ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
            WHERE {bbb_attendance_users}.userid IN '.$lector_sql.' 
            AND {bbb_attendance}.timestamp > '.($course->startdate - 3600).'
            AND {bbb_attendance}.courseid = '.$course->id.'
            ');

            $this->finishTime = $DB->get_field_sql('
            SELECT max(timestamp) 
            FROM {bbb_attendance} 
            INNER JOIN {bbb_attendance_users} 
            ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
            WHERE {bbb_attendance_users}.userid IN '.$lector_sql.' AND {bbb_attendance}.timestamp < '.($course->enddate + 3600).'
            AND {bbb_attendance}.courseid = '.$course->id.'
            ');

    
    }


    public function renderBasicAttendance()
    {
        global $DB;

        //echo "SELECT id FROM {grade_items} WHERE courseid = ".$this->course->id." AND itemname = 'dochazka'";
        $records = $DB->get_records_sql("SELECT {grade_grades}.id,{grade_grades}.itemid,{grade_grades}.userid,{grade_grades}.usermodified,{grade_grades}.finalgrade
              FROM {grade_grades},{user} WHERE itemid = ".$this->gradeInstanceId." AND {grade_grades}.userid = {user}.id ORDER BY {user}.lastname");
      
        $context = $DB->get_field_sql("SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = ".$this->course->id."");
        
        $users =  $DB->get_fieldset_sql("SELECT userid FROM {role_assignments} WHERE roleid = 5 AND contextid = ".$context."");
      
        $record_users = $DB->get_fieldset_sql("SELECT userid FROM {grade_grades} WHERE itemid = ".$this->gradeInstanceId."");
  
    $output = 'Docházka vidokonferencí neobsahuje validní data. Stále ale můžete použít standardní docházku.<br>
            Pokud zde opravdu mají být data, zkontrolujte, zda začátek a konec kurzu odpovídá času, kdy byla konference provedena.<br>
            <b> Zároveň tento systém zaznamenává kurzy od 1.12.2020</b>';
    $output .= "<form action='AJAX/dochazka_process.php' method='post'>";
    $output .= "<table border = 1 style='text-align:center;'>";
    $output .= "<script type='text/javascript' src='check.js'></script>";
            $output .= "<tr>
            <td width='40px'>
            <input type='checkbox' id='selectall' onClick='selectAll(this)' value='selected'></td><td>".'<b>Jméno a příjmení</b>'."</td></tr>";
    $output .= "
    <input type='text' name='courseid' value='".$this->course->id."' hidden>
    <input type='text' name='itemid' value='".$this->gradeInstanceId."' hidden>";       
    foreach  ($records as $record){
    $user = $DB->get_record('user',array('id' => $record->userid));
    if($record->finalgrade == 1){$checked = "checked";} else {$checked = "";}
    $output .= "<tr>
    <td><input type='checkbox' value=".$record->userid." name = 'selectedusers[]' ".$checked."></td><td style='text-align:left;padding: 5px; '> "." ".$user->firstnamephonetic." ".$user->lastname." ".$user->firstname." ".$user->lastnamephonetic."</td>
    <tr>";
    
    }
    $output .= "<tr><td colspan=2><input type='submit'></td></tr>
            </table>
          </form>"; 
      
      
      
        return $output;
    }



    public function renderBBBAttendance()
    {   global $DB,$CFG;
        
        
        
        $realtime = $DB->get_fieldset_sql('
            SELECT timestamp 
          FROM {bbb_attendance} 
          INNER JOIN {bbb_attendance_users} 
            ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
          WHERE  {bbb_attendance}.timestamp > '.($this->startTime - 59).'
          AND {bbb_attendance}.timestamp < '.($this->finishTime + 59).'
          AND {bbb_attendance}.courseid = '.$this->course->id.'
            GROUP BY timestamp
            ORDER BY timestamp ASC
        ');
        $time_points = sizeof($realtime);
        $realtime = (sizeof($realtime)*120)-3600;
        
        $filename = "`EXPORT-ESO-".time().".csv`";
      $output = '
      <form action="AJAX/dochazka_process.php" method="post">
      <table border = 1 id="exportable" style="border-collapse: collapse;">
      <tr>
            <th colspan = 15><img id="head-picture" src="'.$CFG->wwwroot.'/local/bbb_attendance/assets/img/head.jpg">
            </th>
      </td>
        <tr class="table-info">
          <td colspan = 15>Docházka pro kurz: '.$this->course->fullname.'</td>
    
        </tr>
        <tr class="table-info"> 
          <td colspan = 15 id="lector-sign">Plánovaný čas kurzu: '.date('d.m.Y G:i',$this->course->startdate).' - '.date('G:i',$this->course->enddate).'</td>
        </tr>
        <tr class="table-info">
          <td colspan = 15>Hodnoty konference: začátek: '.date('d.m.Y G:i',$this->startTime).' - konec: '.date('d.m.Y G:i',$this->finishTime).' - celkové trvání včetně přestávek: '.date('H:i',$realtime).'</td>
        </tr>
        <tr class="table-info">
        <td colspan = 15 class="to-hide"> 
        
        <b>Zvolit všechny, kteří mají docházku více než: <input type="text" placeholder="70" class="hours" id="hoursInput" maxlength="4" size="4"></input>%<b>
        <input type = "button" value="zvolit" id="percentageselector" onClick="showWhoPasses()"></input><br>
        <input type="text" name="courseid" value="'.$this->course->id.'" hidden></input>
        <input type="text" name="itemid" value="'.$this->gradeInstanceId.'" hidden></input>
        <input type="submit" value="Odeslat">
        
    <button onclick="exportTableToCSV('.$filename.')">Odeslat a stáhnout CSV</button>
    <button>Odeslat</button></input>
    <button onclick="exportTableToPDF('.$filename.')">stáhnout PDF</button>
        </td>
      </tr>
        <tr class="table-header">
          <td><input class="check-header" type="checkbox" id="selectall" onClick="selectAll(this)"></input></td>
          <td>role</td>
          <td>titul</td>
          <td>jmeno</td>
          <td>prijmeni</td>
          <td>titul</td>
          <td>datum narozeni</td>
          <td class="to-hide">obec bydliste</td>
          <td class="to-hide">misto zamestnani</td>
          <td>email</td>
          <td class="to-hide">kurz</td>
          <td class="to-hide">datum kurzu</td>
          <td>cas pripojeni</td>
          <td>cas odpojeni</td>
          <td class="to-hide">cas v kurzu</td>
          
        </tr> ';

        $sql_all = 'SELECT * 
        FROM {bbb_attendance} 
        INNER JOIN {bbb_attendance_users} 
          ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
        WHERE  {bbb_attendance}.timestamp > '.($this->startTime -59).'
        AND {bbb_attendance}.timestamp < '.($this->finishTime + 59).'
        AND {bbb_attendance}.courseid = ?';
        
        $scammers_sql = '
          SELECT {bbb_attendance_users}.userid,{bbb_attendance}.courseid FROM {bbb_attendance_users}
          INNER JOIN {bbb_attendance} ON {bbb_attendance}.id = {bbb_attendance_users}.bbb_attendance_id
          WHERE bbb_attendance_id IN
            (SELECT id FROM {bbb_attendance}
             WHERE {bbb_attendance}.timestamp > '.($this->startTime -59).'
              AND {bbb_attendance}.timestamp < '.($this->finishTime + 59).'
              AND {bbb_attendance}.courseid != '.$this->course->id.')
              GROUP BY {bbb_attendance_users}.userid,{bbb_attendance}.courseid';

       
        
        $all = $DB->get_records_sql($sql_all,array($this->course->id));

        $scamers = $DB->get_records_sql($scammers_sql);

 

        foreach ($this->lectors as $user){
          $checked= '';
         // $output .= render_bbb_attendance_for_user($user,$this->course,'moderátor',$this->startTime,$this->finishTime,$time_points,$checked);
          $output .= render_bbb_attendance_for_user_new($user,$this->course,'moderátor',$this->startTime,$this->finishTime,$time_points,$checked,$all);
        }

        $output .= '<tr>
        <td height="50px"  colspan = "15"></td>
        </tr>';  

        foreach ($this->students as $user){
          $record = $DB->get_field_sql("SELECT {grade_grades}.finalgrade
          FROM {grade_grades} 
          WHERE itemid = ".$this->gradeInstanceId." AND userid = ".$user."");
            if($record == 1){$checked = "checked";} else {$checked = "";} 
            $output .= render_bbb_attendance_for_user_new($user,$this->course,'účastník',$this->startTime,$this->finishTime,$time_points,$checked,$all,$scamers);
        //  $output .= render_bbb_attendance_for_user($user,$this->course,'účastník',$this->startTime,$this->finishTime,$time_points,$checked);
          


        }

      

      $output .='</table><div class="to-hide">
      <input type="submit" value="Odeslat"> <button onclick="exportTableToCSV('.$filename.')">Odeslat a stáhnout CSV</button>
      <button>Odeslat</button></input><button onclick="exportTableToPDF('.$filename.')">stáhnout PDF</button>
      </form>';
      $output .='<br><br><a href="'.new moodle_url('/local/bbb_attendance/attendance_setup.php',array('id' => $this->course->id,'bbbid' => $this->bbbId)).'"><input type = "button" value="Upravit nesrovnalosti"></input></a></div>';

      return $output;
    }



/*********
 * 
 * 
 * prvni zapis do DB, pripraveno pro upravy
 * 
 ******/


    public function createBBBAttendanceToAlter()
    {   global $DB,$CFG;
        
              
        $realtime = $DB->get_fieldset_sql('
        SELECT timestamp 
      FROM {bbb_attendance} 
      INNER JOIN {bbb_attendance_users} 
        ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
      WHERE  {bbb_attendance}.timestamp > '.($this->startTime - 59).'
      AND {bbb_attendance}.timestamp < '.($this->finishTime + 59).'
      AND {bbb_attendance}.courseid = '.$this->course->id.'
        GROUP BY timestamp
        ORDER BY timestamp ASC
        ');
        $time_points = sizeof($realtime);
        $realtime = (sizeof($realtime)*120)-3600;
        



        $sql_header = 'INSERT INTO {bbb_attendance_headers} (bigbluebuttonid,courseid,realstartdate,realenddate,alteredstartdate,alteredenddate)
        VALUES ('.$this->bbbId.','.$this->course->id.','.$this->startTime.','.$this->finishTime.','.$this->startTime.','.$this->finishTime.')';

        $DB->execute($sql_header);

        $headerIdSQL = 'SELECT id FROM {bbb_attendance_headers}  WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'';

        $headerId = $DB->get_field_sql($headerIdSQL);


        foreach ($this->lectors as $user){

            $userSQL =  get_initial_sql_attendance_for_user($user,$this->course,1,$this->startTime,$this->finishTime,$time_points,$headerId);
            $DB->execute($userSQL);


        }

       

        foreach ($this->students as $user){


          $userSQL =  get_initial_sql_attendance_for_user($user,$this->course,5,$this->startTime,$this->finishTime,$time_points,$headerId);
          $DB->execute($userSQL);

        }

   
//tady asi reload?
    
    }


}
/*
$courseid = 1041;
$bbbid = 4532;
$test = new Original_attendance($courseid,$bbbid);
$test->getInitialData();

echo $test->renderBasicAttendance();
//print_r($test);*/