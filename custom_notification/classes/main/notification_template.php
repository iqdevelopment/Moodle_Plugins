<?php
namespace local_custom_notification\main;

class notification_template{
    public $categoryarray;
    public $notificationText;
    public $usedCategories = array();


    public function renderTemplates()
    {   global $DB,$CFG;
        $alreadySet = $DB->get_records_sql('SELECT * FROM {config_plugins} WHERE plugin = ? AND name like "%category_id%"',array('local_custom_notification'));
        $output ='<h2>'.get_string('created_templates','local_custom_notification').sizeof($alreadySet).'</h2>';
        if($alreadySet){
            foreach ($alreadySet as $key => $value) {
                $categoryId = explode('-',$value->name);
                $categoryName = $DB->get_field_sql('SELECT name FROM {course_categories} WHERE id = ?',array($categoryId[1]));
                $output .= '<span class="template-heading"> '.get_string('category_name','local_custom_notification').$categoryName.'</span><br>
                            <div class="template-div">'.nl2br($value->value).'</div>';
                $output .= '<form action="'.$CFG->wwwroot.'/local/custom_notification/AJAX/processTemplate.php" method="post">
                            <input type="hidden" name="delete" value="'.$categoryId[1].'"></input>
                            <input type="submit" value="'.get_string('delete','local_custom_notification').'"></input></form><br><br>';
                
                $usedCategory[] = $categoryId[1];
            }
        }else{
           $output .=  get_string('no_templates','local_custom_notification').'<br>';
           $usedCategory = array();
          // $output .= $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical')); 
           //$obj = renderDoneTemplates($alreadySet);  
        }
        $this->usedCategories = $usedCategory;
        return $output;
         //renderTemplateCreator($obj->usedCategory);
    }


    public function renderTemplateCreator()
    {   global $DB,$CFG;
        $allowedCategories = $DB->get_field_sql('SELECT value FROM  {config_plugins}  WHERE plugin = ? AND name = ?',array('local_custom_notification','categories')); 
        $allowedCategories = explode(',',$allowedCategories);
        $allowedCategoriesFinal = array_diff($allowedCategories,$this->usedCategories);
        $output = '<h2>'.get_string('template_creator','local_custom_notification').'</h2>';
        $output .= '<form action="'.$CFG->wwwroot.'/local/custom_notification/AJAX/processTemplate.php" method = "POST">';
        $output .='<div class="form-container">';
        $output .= '<select id="select" name="categories[]" multiple size = 10>';
        foreach ($allowedCategoriesFinal as $value) {
           $placeholder = $DB->get_field_sql('SELECT name FROM {course_categories} WHERE id = ?',array($value));
           $output .= '<option value="'.$value.'">'.$placeholder.'</option>';
        }
        $output .= '</select>';
        $output .= '<textarea id="textarea" name="text" rows="8">'.$DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical')).'</textarea>';
        $output .= '<input type="checkbox" name="reset-all" onClick="resetAllWarninig(\''.get_string('resetAllWarninig','local_custom_notification').'\')"> '.get_string('overwrite_all_course_in_category','local_custom_notification').'</input>';
        $output .= '</div>';
        $output .= '<br><input type="submit"></input>';
        $output .= '</form>';
       return $output;
    }


    public function fillNotificationTask()
    {   global $DB;
        $allowed = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','categories'));
        $sql = 'SELECT id,category 
                    FROM {course} 
                    WHERE category IN ('.$allowed.') 
                    AND id NOT IN 
                    (SELECT courseid FROM {custom_notification} GROUP BY courseid)';

        $toFill = $DB->get_records_sql($sql,array('local_custom_notification','categories'));
        $preSetTemplates = $DB->get_records_sql('SELECT name,value FROM {config_plugins} WHERE plugin = ? AND name like "%category_id-%"',array('local_custom_notification'));
        foreach ($preSetTemplates as $key => $value) {
            $exploded = explode('-',$value->name);
            $value->categoryId = $exploded[1];
            $ThisCategoryHasTemplate[] = $value->categoryId;
           
        }

        $sql = 'INSERT INTO {custom_notification} (courseid,type,sent,enabled,timecreated,text) VALUES (?,?,?,?,?,?)';
        $days = array(1,3,7);
         foreach ($days as $key => $value) {
            $dayArray[$value]['enabled'] = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = "type_'.$value.'"',array('local_custom_notification'));
        }
       $defaultRegullarNotification =  $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical'));

       // print_r($preSetTemplates);

        foreach ($toFill as $key => $value) {
           if (in_array($value->category,$ThisCategoryHasTemplate)){
               $nameOfNotificationArray = 'category_id-'.$value->category;
               
                foreach ($dayArray as $dayKey => $dayValue) {
                   // echo $value->id. '----'. $dayKey .'--'.$dayValue['enabled'].'<br>';
                     $DB->execute($sql,array($value->id,$dayKey,0,$dayValue['enabled'],time(),$preSetTemplates[$nameOfNotificationArray]->value));
                }

           }else{
                foreach ($dayArray as $dayKey => $dayValue) {
                   // echo $value->id. '----'. $dayKey .'--'.$dayValue['enabled'].'<br>';
                     $DB->execute($sql,array($value->id,$dayKey,0,$dayValue['enabled'],time(),$defaultRegullarNotification));
                }

           }
               
              
        }
    }





}