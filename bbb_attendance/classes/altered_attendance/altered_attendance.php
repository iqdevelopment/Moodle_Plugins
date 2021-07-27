<?php

//require_once('../../../../config.php');
//require_once('../../lib.php');
global $DB;

class Altered_attendance{

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
        $this->header = $DB->get_field_sql('SELECT id FROM {bbb_attendance_headers} WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'');
        $this->realStartTime = $DB->get_field_sql('SELECT realstartdate FROM {bbb_attendance_headers} WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'');
        $this->realFinishTime = $DB->get_field_sql('SELECT realenddate FROM {bbb_attendance_headers} WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'');
        $this->alteredStartTime = $DB->get_field_sql('SELECT alteredstartdate FROM {bbb_attendance_headers} WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'');
        $this->alteredFinishTime = $DB->get_field_sql('SELECT alteredenddate FROM {bbb_attendance_headers} WHERE courseid = '.$this->course->id.' AND bigbluebuttonid = '.$this->bbbId.'');

    }




   
    public function renderBBBAttendance()
    {   global $DB,$CFG;
        
        
        
        $realtime = $this->alteredFinishTime - $this->alteredStartTime ;

    
        
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
          <td colspan = 15 id="lector-sign">>Plánovaná čas kurzu: '.date('d.m.Y G:i',$this->course->startdate).' - '.date('G:i',$this->course->enddate).'</td>
        </tr>
        <tr class="table-info">                                                                                                                                                              
          <td colspan = 15>Hodnoty konference: začátek: '.date('d.m.Y G:i',$this->alteredStartTime).' - konec: '.date('d.m.Y G:i',$this->alteredFinishTime).' - celkové trvání včetně přestávek: '.date('H:i',$realtime-3600).'</td>
        </tr>
        <tr class="table-info">
        <td colspan = 15 class="to-hide"> 
        
        <b>Zvolit všechny, kteří mají docházku více než: <input type="text" placeholder="70" class="hours" id="hoursInput" maxlength="4" size="4"></input>%<b>
        <input type = "button" value="zvolit" id="percentageselector" onClick="showWhoPasses()"></input><br>
        <input type="text" name="courseid" value="'.$this->course->id.'" hidden></input>
        <input type="text" name="itemid" value="'.$this->gradeInstanceId.'" hidden></input>
        <input type="submit" value="Odeslat"><button onclick="exportTableToCSV('.$filename.')">Odeslat a stáhnout CSV</button></input>
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

        foreach ($this->lectors as $user){
          $output .= render_altered_bbb_attendance_for_user($user,$this->course,'moderátor',$this->alteredStartTime,$this->alteredFinishTime,$this->header,$checked);
        }

        $output .= '<tr>
        <td height="50px"  colspan = "15"></td>
        </tr>';  

        foreach ($this->students as $user){
          $record = $DB->get_field_sql("SELECT {grade_grades}.finalgrade
          FROM {grade_grades} 
          WHERE itemid = ".$this->gradeInstanceId." AND userid = ".$user."");
            if($record == 1){$checked = "checked";} else {$checked = "";} 
          $output .= render_altered_bbb_attendance_for_user($user,$this->course,'účastník',$this->alteredStartTime,$this->alteredFinishTime,$this->header,$checked);

        }

      

      $output .='</table><div class="to-hide">
      <input type="submit" value="Odeslat"> <button onclick="exportTableToCSV('.$filename.')">Odeslat a stáhnout CSV</button>
        <button>Odeslat</button></input>
      <button onclick="exportTableToPDF('.$filename.')">stáhnout PDF</button>
      </form>';
      $output .='<br><br><a href="'.new moodle_url('/local/bbb_attendance/attendance_setup.php',array('id' => $this->course->id,'bbbid' => $this->bbbId)).'">
      <input type="button" value="Upravit nesrovnalosti"></input></a>
      </div>';


      return $output;
    }



    public function renderBBBAttendanceToAlter()
    {   global $DB,$CFG;
        
        
      $realRealTime = $this->realFinishTime - $this->realStartTime;
        $realtime = $this->alteredFinishTime - $this->alteredStartTime;

    
        
    
      $output = '<h1>úprava docházky</h1>
      <b>Docházka pro kurz: </b>'.'<a href="'.$CFG->wwwroot."/course/view.php?id=".$this->course->id.'">'.$this->course->fullname.'</a><br>
      <a href="'.$CFG->wwwroot."/local/bbb_attendance/attendance.php?id=".$this->course->id.'"><button>Zpět k docházce</button></a><br><br>
      <form action="AJAX/dochazka_alter_form_send.php" method="post">
      <input type="text" name="courseid" value="'.$this->course->id.'" hidden>
      <input type="text" name="bbbid" value="'.$this->bbbId.'" hidden>
      <table border = 1>

        <tr class="table-info">
          <td colspan = 15>Docházka pro kurz: '.$this->course->fullname.'</td>
    
        </tr>
        <tr class="table-info"> 
          <td colspan = 15 id="lector-sign">Plánovaný čas kurzu: '.date('d.m.Y G:i',$this->course->startdate).' - '.date('G:i',$this->course->enddate).'</td>
        </tr>
        <tr class="table-info">
          <td colspan = 5>Původní hodnoty konference:</td>
          <td colspan = 1> začátek:</td>
          <td colspan = 2> '.date('d.m.Y G:i',$this->realStartTime).'</td>
          <td colspan = 1>konec: </td>
          <td colspan = 3>'.date('d.m.Y G:i',$this->realFinishTime).'</td>
          <td colspan = 3>celkové trvání přestávek: '.date('H:i',$realRealTime-3600).'</td>
          </tr>
        <tr class="table-info">
        <td colspan = 5>Aktuální hodnoty konference:</td>
        <td colspan = 1> začátek:</td>
        <td colspan = 2> '.date('d.m.Y',$this->alteredStartTime).'<input id="startTimeChange" onchange="timeChange(`startTimeChange`)" type="time" name="alteredstartdate" value = "'.date('H:i',$this->alteredStartTime).'"></input></td>
        <td colspan = 1> konec:</td>
        <td colspan = 3> '.date('d.m.Y',$this->alteredFinishTime).'<input id="endTimeChange" onchange="timeChange(`endTimeChange`)" type="time" name="alteredenddate"  value = "'.date('H:i',$this->alteredFinishTime).'"></input></td>
        <td colspan = 3>celkové trvání přestávek se počítá: <span id="countTime">'.date('H:i',$realtime-3600).'</span></td>
        </tr>
        <tr class="table-info">
        <td colspan = 15> 
        
        <b>Při změně začátku, nebo konce konference se přepočítají hodnoty pro všechny uživatele podle % v kurzu. Je pak potřeba vše odeslat pro zápis do databáze!! <b><br>

        <input type="submit" onclick="zeroesAreNumbers()" value="Odeslat změny"></input>
        </td>
      </tr>
        <tr class="table-header">
          <td> / </td>
          <td>role</td>
          <td>titul</td>
          <td>jmeno</td>
          <td>prijmeni</td>
          <td>titul</td>
          <td>původní připojení</td>
          <td>upravené připojení</td>
          <td>původní odpojení</td>
          <td>upravené odpojení</td>
          <td>původní % účasti</td>
          <td>upravené % účasti</td>
          <td>původní poznámka</td>
          <td>smazat?</td>
          
        </tr> ';

        foreach ($this->lectors as $user){
          $output .= render_altered_to_setup_bbb_attendance_for_user($user,$this->course,'moderátor',$this->alteredStartTime,$this->alteredFinishTime,$this->header,$checked);
        }

        $output .= '<tr>
        <td height="50px"  colspan = "15"></td>
        </tr>';  

        foreach ($this->students as $user){
          $record = $DB->get_field_sql("SELECT {grade_grades}.finalgrade
          FROM {grade_grades} 
          WHERE itemid = ".$this->gradeInstanceId." AND userid = ".$user."");
            if($record == 1){$checked = "checked";} else {$checked = "";} 
          $output .= render_altered_to_setup_bbb_attendance_for_user($user,$this->course,'účastník',$this->alteredStartTime,$this->alteredFinishTime,$this->header,$checked);

        }

      

      $output .='</table>
      <input type="submit" value="Odeslat změny" onclick="zeroesAreNumbers()"></input>
      </form>';
     

      return $output;
    }




}