import Category from './category.js'
import User from './users.js'
import Course from './courses'
import * as listener from './listeners.js'
import * as general from './output.js'
import AfterRenders from './afterRenders.js'


export function searchText(searchText) {
    //console.log(searchText);
    const id = general.renderFullLoader('#results');

    $.ajax({
        url:"AJAX/AJAX.php",
        method:"POST",
        data:{searchAll:searchText},
        dataType:"json",
        success:function(data)
        {   
          //console.log(data);
                      //render users
            const user = new User(USER);  
                user.renderUserListHead();
                for (const [key, value] of Object.entries(data.foundUsers)) {  
                user.renderListOfUsersItem(value);
                }
                user.renderUserListEnd();
                AfterRenders.userPanelListeners();

            //render courses
            Course.renderCourseListHead();
              for (const [key, value] of Object.entries(data.foundCourses)) {  
                Course.renderListOfCoursesItem(value);
              }
              Course.renderCourseListEnd();
              general.dragOverUsersToCourses('course-list-li');
              AfterRenders.coursePanelListListeners();

            //category renders
            Category.renderBasicCategoriesForUser(data.contextArray);
            AfterRenders.categoriesShowCategoryTreeButton();

                //console.log(data);

          
        },error:function (data){
         
          //console.log(data);
        }
      })
      general.cancelLoader(id);
       
}


