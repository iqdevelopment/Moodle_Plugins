import Category from './category.js'
import User from './users.js'
import AfterRenders from './afterRenders.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import * as general from './output.js'
import * as search from './search'
import * as styles from './../css/style.scss'

/* let obj = new Category(3);
obj.categoryInfo();
//console.log(obj);
output.showTree(3); */


/* document.querySelector(`.show-my-categories`).addEventListener("click",function(){output.showMyCategories(USER);});
function test() {
    //console.log('test');
}
// */

//on load what need to happen

    AfterRenders.myCategoriesButton();
    AfterRenders.myAdminCategories();
    AfterRenders.searchButton();
    general.showMyCategories(USER);




