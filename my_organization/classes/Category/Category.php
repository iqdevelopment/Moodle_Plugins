<?php
namespace local_my_organization\Category;

class Category{

    public $name;
    public $idnumber;
    public $parent;
    

    public function __construct($name = null,$idnumber = null,$category_id = null)
    {
        $this->name = $name;
        $this->idnumber = $idnumber;
        $this->id = $category_id;
    }

        /****
         * 
         * 
         * checks if created object exists
         * 
         */

    private function existCheck()
    {   global $DB;
        if($this->id){
            $sql_id = 'SELECT * FROM {course_categories} WHERE id = ?';
            $check_id = $DB->get_record_sql($sql_id,array($this->id));
            if($check_id){
                $this->name = $check_id->name;
                $this->idnumber = $check_id->idnumber;
                $this->exists = 1;
                return 1;    
            }
        }   
        $sql = 'SELECT * FROM {course_categories} WHERE name = ? AND idnumber = ?';
        $check = $DB->get_record_sql($sql,array($this->name,$this->idnumber));
        if ($check) {
            $this->exists = 1;
            $this->id = $check->id;
            return 1;
        }else{
            $this->exists = 0;
            return 0;
        }
       
    }

    public function getCategoryInfo()
    {   global $DB;
        $this->existCheck();
        if($this->exists == 1){
            $this->getCategoryChildren();
            $this->contextid = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 40 AND instanceid = ?',array($this->id));
            $this->parent = $DB->get_field_sql('SELECT parent FROM {course_categories} WHERE id =?',array($this->id));
            $this->path = $DB->get_field_sql('SELECT path FROM {course_categories} WHERE id = ?',array($this->id));
        }
    }

    /*
     * 
     * simple class to create new course category
     *  $data->name 
     *  $data->idnumber = 'zapis2';
        $data->parent = 136;
        $category = category\category::createCategory($data); 
     * 
     */

    public function createCategory($parentid)
    {   
        $output = new \stdClass();
 
            
        $this->parent = $parentid;
        $this->getCategoryInfo();
        if($this->exists == 0){
            $category = \core_course_category::create($this);
            $this->createCohort();
            $this->getCategoryInfo();
            $output->type = 'positive';
            $output->data = get_string('category_created','local_my_organization');
            return $output;
        }else{
            $output->type = 'negative';
            $output->data = get_string('category_exists','local_my_organization');
            return $output;
        }
    }

    
    /*
     * 
     * update category by new Name, idnumber and new parent id
     * 
     */


    public function updateCategory($new_name = null,$new_idnumber = null,$new_parrent = null)
    {   global $DB;

        $this->getCategoryInfo();
        if($this->exists == 0){
            return get_string('category_not_exists','local_my_organization');
        }
        $this->name = ($new_name == '') ? $this->name : $new_name; 
        $this->idnumber = ($new_idnumber == '') ? $this->name : $new_idnumber; 
        $this->parent = ($new_parrent == '') ? $this->parent : $new_parrent;
        
        $sql = 'UPDATE {course_categories} SET name = ?, idnumber = ?, parent = ? WHERE id = ?';
        $DB->execute($sql,array($this->name,$this->idnumber,$this->parent,$this->id));
        $this->updateCohort();
        return $category;
    }

    /****
     * 
     * funcion to complete deletion of Category including courses
     * 
     */


    public function deleteCategoryAll()
    {
        global $DB,$CFG;
        $output = new \stdClass();
        $this->getCategoryInfo();
        $this->getCategoryChildren();
        // delete also cohorts and cohort user assignments for childer categories
        foreach ($this->categoryChildren->categories as $key => $value) {
            $delete_obj = new Category('','',$value->id);
            $delete_obj->getCategoryInfo();
            $delete_obj->deleteCohort();
        }
        
        
        //print_r($this);
        $category = \core_course_category::get($this->id);

        $deletedcourses = $category->delete_full(true);
        $output->type = 'positive';
        $message = $this->checkIfDefaultCategoryWasDeleted();
        $output->data = $message;  
        return $output;
    }

    /****
     * 
     * function to delete Category and move courses to new parent
     * 
     */

    public function deleteCategoryMove($new_parent_id)
    {
        global $DB;
        $output = new \stdClass();
        $this->getCategoryInfo();
        $category = \core_course_category::get($this->id);
        $output->type = 'positive';
        $category->delete_move($new_parent_id, true);
        $output->data =  $this->checkIfDefaultCategoryWasDeleted();
        return $output;
    }


    /* 
    *
    function for AJAX call to show direct childeren of given category and courses directly in this category
    *
    */
    public function getCategoryChildren()
    {   
        global $DB;
        $returnObject = new \stdClass();
        $initial_category = $DB->get_record_sql('SELECT * FROM {course_categories} WHERE id = ?  ORDER BY name',array($this->id));
        $max_depth = $DB->get_field_sql('SELECT max(depth) FROM {course_categories} WHERE path like "%/'.$initial_category->id.'/%"');

        $sql_for_category_childern = 'SELECT * FROM {course_categories} WHERE parent = ?';
        $sql_for_course_in_category = 'SELECT * FROM {course} WHERE category = ?';
        $returnObject->courses = $DB->get_records_sql($sql_for_course_in_category,array($this->id));
        $returnObject->categories = $DB->get_records_sql($sql_for_category_childern,array($this->id));

        $this->categoryChildren = $returnObject;
        return $returnObject; 
    }



    public function getCategoryChildrenIds($contextArray)
    {   global $DB;
        $return = array();
        foreach ($contextArray as $key => $value) {
            $sql = 'SELECT id FROM {course_categories} WHERE path like "%/'.$value->categoryid.'/%"';
            $output = $DB->get_fieldset_sql($sql);
            $return = array_merge($return,$output);
        }
        
        return $return;
    }

    /***
     * 
     * function to get whole tree of givven category
     * this is for rendering initial tree
     * 
     * 
     */


    public function getCategoryFullTree()
    {   
        global $DB;
        $this->getCategoryInfo();
        $pathstring = $this->path.'/';
        $children = $DB->get_records_sql('SELECT id,name,idnumber,parent,path FROM {course_categories} WHERE path like "%'.$pathstring.'%" OR id = ? ORDER BY name',array($this->id));
        $sub_obj = $this->buildTree($children,$this->parent);
       // print_r($sub_obj);
        foreach ($sub_obj as $key => $value) {
            if(isset($value->children)){
                $this->fullTree = $value->children;
            }
        }
        
         
    }
    
    /**
     * 
     * recursive function to get whole tree -> this is mainly for printing the category tree
     * 
     */

    private function buildTree($elements, $parentId = 0) {
        //$return_object = new \stdClass();
        $branch = array();

        foreach ($elements as $element) {
            if ($element->parent == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                 if ($children) {
                    $element->children = $children;
                } 
                $branch[] = $element;
            }
        }

    return $branch;
    }

//$tree = buildTree($rows);
    



    /******
     * 
     * 
     * function for task, to create new cohort with new course category
     * 
     */

     public static function TaskcreateCohortFromCourseCategory()
     {  global $DB; 
        $sql = 'SELECT id FROM {course_categories} WHERE idnumber NOT IN (SELECT idnumber FROM {cohort})';
        $categories_to_fill = $DB->get_fieldset_sql($sql);
        foreach ($categories_to_fill as $category) {
            $obj = new Category('','',$category);
            $obj->createCohort();
            echo 'created cohort for '.$obj->name.'<br>';
        }
        
     }


    /****
     * 
     * 
     * function to create new cohort in the same time as category is created
     * 
     */
    private function createCohort()
    {   global $DB;
        $this->getCategoryInfo();
        $context_id = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 40 AND instanceid = ?',array($this->id));
        $sql = 'INSERT INTO {cohort} (contextid,name,idnumber,description,descriptionformat,visible,component,timecreated,timemodified,theme)
                        values (?,?,?,?,?,?,?,?,?,?)';
        $DB->execute($sql,array($context_id,$this->name,$this->idnumber,'',1,1,'local_my_organization',time(),time(),''));
    }

    /****
     * 
     * 
     * function to update cohort name a and idnumber
     * 
     */
    private function updateCohort()
    {   global $DB;
        $category_id = $DB->get_field_sql('SELECT id FROM {course_categories} WHERE idnumber = ?',array($this->idnumber));
        $context_id = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 40 AND instanceid = ?',array($category_id));
        $sql = 'UPDATE {cohort}
                    SET name = ?,
                        idnumber = ?,
                        timemodified = ?
                    WHERE contextid = ? AND component = ?';
        $DB->execute($sql,array($this->name,$this->idnumber,time(),$context_id,'local_my_organization'));
    }

    /****
     * 
     * 
     * function to delete cohort, including all cohort assignments and user role assignments
     * 
     */
    private function deleteCohort()
    {   global $DB;
        $sql = 'DELETE FROM {cohort} WHERE name = ? AND component = ?';
        $sql_users_cohort = 'DELETE FROM {cohort_members} WHERE cohortid = (SELECT id FROM {cohort} WHERE name = ? AND component = ?)';

        $DB->execute($sql_users_cohort,array($this->idnumber,'local_my_organization'));
        $DB->execute($sql,array($this->idnumber,'local_my_organization'));
    }



     private function checkIfDefaultCategoryWasDeleted()
     {  global $DB;
        $this->getCategoryInfo();
        $check_if_deleted_default_category = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_category','local_my_organization'));
        if($check_if_deleted_default_category == $this->id){
            $url = $url = new \moodle_url("/admin/settings.php",array('section'=> 'local_my_organization_settings'));
            $message = get_string('deleted_default_category','local_my_organization');
           // redirect($url,$message,0);
            return $message;
        }else{
            return get_string('deleted','local_my_organization');
        } 
     }



     static function validateInput($string)
     {
        
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $string)){
            return FALSE;
        }else{
            return $string;
        }
        
     }

     public function getCategoryUsers()
     {  global $DB;
        $sql = 'SELECT id as roleid,name,shortname FROM {role} WHERE id IN 
                (
                    SELECT roleid FROM {role_context_levels} WHERE contextlevel = 40
                )
            ';
        $roles = $DB->get_records_sql($sql,array($this->id));

        foreach ($roles as $role) {
            $sql = 'SELECT {user}.id,{user}.firstname,{user}.lastname,{user}.email
                    FROM {user} 
                        INNER JOIN {role_assignments} ON {role_assignments}.userid = {user}.id
                    WHERE {role_assignments}.contextid = ? 
                    AND {role_assignments}.roleid = ?

            
                ';


            $role->users = $DB->get_records_sql($sql,array($this->contextid,$role->roleid));
        }

        $this->roles = $roles;
     }









}