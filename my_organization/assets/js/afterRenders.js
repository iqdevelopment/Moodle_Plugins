import Category from './category.js'
import User from './users.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import * as general from './output.js'
import * as search from './search'

export default class AfterRenders{

/***
 * 
 * event listener for assigning event listener to mycategories button
 * 
 */

    static myCategoriesButton(){
        document.querySelector(`.show-my-categories`).addEventListener("click",function(){general.showMyCategories(USER);});
    }


    /***
 * 
 * event listener for assigning event listener to mycategories button
 * 
 */

     static myAdminCategories(){
         if(document.querySelector(`.admin-categories`)){
            document.querySelector(`.admin-categories`).addEventListener("click",function(){general.showAdminMyCategories();});
        }
    }


/****
 * 
 * event listener, after list of my (or admin categories) is rendered
 * 
 */
    static categoriesShowCategoryTreeButton(){
        for (const [key, value] of Object.entries(document.querySelectorAll('.category-main-categories'))) {
            let categoryid = value.getAttribute('category');
            value.addEventListener("click",function(){general.showTree(categoryid);}) 
        }

        AfterRenders.categorySettingsButtons();
    }


    /**
     * 
     * function to create event listeners for category addional buttons
     * 
     */
    static categorySettingsButtons(){

        for (const [key, value] of Object.entries(document.querySelectorAll('.add-category'))) {
            let categoryid = value.getAttribute('category');
            value.addEventListener("click",function(){general.createCategoryForm(categoryid);}) 
        }

        for (const [key, value] of Object.entries(document.querySelectorAll('.delete-category'))) {
            let categoryid = value.getAttribute('category');
            value.addEventListener("click",function(){general.deleteCategoryForm(categoryid);}) 
        }

        for (const [key, value] of Object.entries(document.querySelectorAll('.edit-category'))) {
            let categoryid = value.getAttribute('category');
            value.addEventListener("click",function(){general.updateCategoryForm(categoryid);}) 
        }
       /*  onclick="createCategoryForm(${id})" // add-category
        onclick="deleteCategoryForm(${id})" class="delete-category
        onclick="updateCategoryForm(${id})" class="edit-category */

    }


    /****
     * 
     * event listener for closing popup windows
     * 
     */

    static closeWindow(){
        let smallWindow = document.querySelector('.close-btn');
        if(smallWindow){
            smallWindow.addEventListener("click",function(){general.closeForm() ;}) ;
        }

        let bigWindow = document.querySelector('.close-btn-info-window');
        if(bigWindow){
            bigWindow.addEventListener("click",function(){general.closeInfoWindow() ;}) ;
        }
    }

    /***
     * 
     * event listener for sending new category form, and generated the idname
     * 
     */
     static createCategory(){
        const form = document.querySelector('.create-category');
        const parentid = form.getAttribute("parentid");
       form.addEventListener("click", function(){general.createCategory(parentid)});

         const change = document.querySelector('#new-category-name');
        change.addEventListener("keyup", function(){general.createCategoryIdname()}); 

     }

    /***
     * 
     * event listener to control delete category form
     * 
     */
     static deleteCategory(){
        const form = document.querySelector('.delete-category');
        const categoryid = form.getAttribute("category");
       form.addEventListener("click", function(){general.deleteCategory(categoryid)});
       

       const deleteForm = document.querySelector('#delete-category-select');
       const category = form.getAttribute("category");
       deleteForm.addEventListener("change", function(){general.whereToMoveDeletedCourses(category)});

        //id = "delete-category-select" onChange="whereToMoveDeletedCourses(${id})"
     }



     /****
      * 
      * listener for user panel
      * 
      */
     static userPanelListeners(){
        for (const [key, value] of Object.entries(document.querySelectorAll('.user-list-li'))) {
            let userid = value.getAttribute('userid');
            value.addEventListener("click",function(){User.checkUser(userid);});
            value.addEventListener("dragstart",function(){general.dragWatterFall(event);});
            value.addEventListener("dragend",function(){general.dragWatterFallEnd(event);});
        }

        for (const [key, value] of Object.entries(document.querySelectorAll('.checkbox-user-li'))) {
            value.addEventListener("click",function(event){
                let userid = value.getAttribute('userid')
                event.stopPropagation();
                User.checkUser(userid,true);
            });
        }

        for (const [key, value] of Object.entries(document.querySelectorAll('.user-more-info'))) {
            let userid = value.getAttribute('userid');
            value.addEventListener("click",function(){general.userInfo(userid);});           
        }
        const checkbox = document.getElementById('user-count-control');
        const category = checkbox.getAttribute("category");
        checkbox.addEventListener("click", function(){general.showTree(category)});

        const searchbox = document.getElementById('search-ul-input');
        searchbox.addEventListener("keyup", function(){User.searchUL()}); 


     }


     /***
      * 
      * user info panel listeners
      * 
      */
     static userInfoPanelListeners(){
        //unasign buttons
       // class="remove-member" contextid="${contextArray}" role="${roleassignid}"
        for (const [key, value] of Object.entries(document.querySelectorAll('.remove-member'))) {
            const contextid = value.getAttribute('contextid');

           // onclick="User.unAsignUserRole(${contextArray.roleassignid})"
            value.addEventListener("click",function(){User.unAsignUserRole(contextid);});
        }

        //update category button
        //categoryid="${value.categoryid}" href="#" onclick="updateCategoryForm(${value.categoryid})
        for (const [key, value] of Object.entries(document.querySelectorAll('.update-category-from-user-form'))) {
            const categoryid = value.getAttribute('categoryid');
            value.addEventListener("click",function(){general.updateCategoryForm(categoryid);});
        }

        //unenroll
        //courseid="${courses.id}" class="unenrol-user-btn" onClick="User.unEnroll(${courses.id})
        for (const [key, value] of Object.entries(document.querySelectorAll('.unenrol-user-btn'))) {
            const courseid = value.getAttribute('courseid');
            const userid = value.getAttribute('value');
           value.addEventListener("click",function(){
                const user = new User(userid);
                user.unEnroll(courseid);
            });
        }


     }


     /*
      * 
      * event listener for Category info window role carousel
      * 
      */
     static categoryInfoPanelListeners(){
         //buttons to switch roles
            for (const [key, value] of Object.entries(document.querySelectorAll('.carousel-btn'))) {
                const roleid = value.getAttribute('roleid');
            value.addEventListener("click",function(){
                    
                    Category.caurouselSwitch(roleid);
                });
            }

            //drag events for role unnasign and change role
            for (const [key, value] of Object.entries(document.querySelectorAll('.drag-category-role-user'))) {
                const roleid = value.getAttribute('roleid');
            value.addEventListener("dragstart",function(){
                    general.showBin(event);
                });
                value.addEventListener("dragend",function(){
                    general.endBin(event);
                });
            }

            for (const [key, value] of Object.entries(document.querySelectorAll('.search-carousel'))) {
                const roleid = value.getAttribute('roleid');
                value.addEventListener("keyup",function(){
                    Category.searchULCarousel(roleid);
                });
                
            }

            

     }



     /*
      * 
      *  event listener for list of courses in right panel 
      * 
      */

     static coursePanelListListeners(){
        for (const [key, value] of Object.entries(document.querySelectorAll('.course-more-info'))) {
            const courseid = value.getAttribute('courseid');
           value.addEventListener("click",function(){
                
                Course.courseInfo(courseid);
            });
        }
        document.getElementById('search-ul-input-courses').addEventListener('keyup',function () {
            Course.searchUL();
        })

        const checkbox = document.getElementById('course-count-control');
        const category = checkbox.getAttribute("category");
        checkbox.addEventListener("click", function(){general.showTree(category)});
     }

      /*
      * 
      *  event listener for list of courses in right panel 
      * 
      */

        static courseDetailListeners(){
            for (const [key, value] of Object.entries(document.querySelectorAll('.course-user-list-li'))) {
                const courseid = value.getAttribute('courseid');
                //click
                value.addEventListener("click",function(){
                    
                    Course.courseInfo(courseid);
                });
                //dragstart
                value.addEventListener("dragstart",function(){
                    Course.showBin(event);
                });
                //dragend
                value.addEventListener("dragend",function(){
                    Course.endBin(event);
                });
            } 

            document.getElementById('search-ul-input-courses-users').addEventListener('keyup',function () {
                Course.searchULList();
                
            })

            document.getElementById('role-filter').addEventListener('change',function () {
                Course.filterCourseUsers();
                
            })
        }

        /*
         *
        listener for search button to initiate global search 
         * 
         */

        static searchButton(){
            
            document.getElementById('search-button').addEventListener('click',function () {
                const searchText = document.getElementById('search-field').value;
                search.searchText(searchText);
            })


        }



}