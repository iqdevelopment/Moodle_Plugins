import view  from './views'
import control  from './controls'

export default class listeners{

    static searchListener() {
        document.getElementById('search-field').addEventListener('keyup',listeners.searchText)
        document.getElementById('search-button').addEventListener('click',listeners.searchText)
    }

    static searchText(ev){
        if(ev.key == 'Enter' || ev.type == 'click'){
            const searchText = document.getElementById('search-field').value
            control.searchTextProcessor(searchText)
            
        }
        
    }


    static scrollListener(){

        document.addEventListener('scroll',listeners.scrollAction)
    }

    static scrollAction(ev){

        if(listeners.scrollDirection()){
            
            if(window.querry != ''){
                control.showSearchedAppend()
                document.removeEventListener('scroll',listeners.scrollAction)
            }else{
                control.showFreshAppend()
                document.removeEventListener('scroll',listeners.scrollAction)
            }
        }
        
    }


    /**
     * reads current position and direction of scrolling, if direction is down and user is in 69% of page, it returs true
     * @returns bolean
     */
    static scrollDirection(){
        const currentscroll = window.pageYOffset
        let windowHeight = window.innerHeight
        const limit = Math.max( document.body.scrollHeight, document.body.offsetHeight, 
                                document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight )
        if(window.previousroll){
            var percentage = (currentscroll/(limit - windowHeight))*100
        }

        
        if(percentage > 69 && window.previousroll < currentscroll){
            window.previousroll = window.pageYOffset
            return true
        }else{
            window.previousroll = window.pageYOffset
            return false
        }

    }


    static moreInfo(ev){
        const target = ev.target
        const rowElement = target.parentNode.parentNode.parentNode
        const id = rowElement.id
        rowElement.classList.add('more-info-line')
        control.getMoreInfo(id)
        listeners.changeicon(target)
        ////console.log(ev.target)
    }

    /**
     * changes the icon when more info is displayed or minimalized
     * @param {*} target 
     */
    static changeicon(target){
        const element = target.parentNode

        if(element.innerHTML == '<i class="fa fa-info-circle"></i>'){
            element.innerHTML = '<i class="fa fa-times-circle"></i>'
            element.removeEventListener('click', this.moreInfo)
            element.addEventListener('click', this.closeInfo)
        }else{
            element.innerHTML = '<i class="fa fa-info-circle"></i>'
            element.addEventListener('click', this.moreInfo)
            element.removeEventListener('click', this.closeInfo)
        }
        ////console.log(target.parentNode)
        
    }

    /**
     * minimalizes the more info field
     * @param {*} ev 
     */
    static closeInfo(ev){
        const target = ev.target
        const rowElement = target.parentNode.parentNode.parentNode
        const sibling = rowElement.nextSibling
        //console.log(sibling)
        sibling.remove()
        listeners.changeicon(target)
        rowElement.classList.remove('more-info-line')

    }

   

}