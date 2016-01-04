<?php
class Zend_View_Helper_Rss extends Zend_View_Helper_Abstract {

    public function rss($title='', $fullUrl='', $items=array(), $other_data=array()) {
        
        $feed = new Zend_Feed_Writer_Feed;
        
        if($title!='') {
            $feed->setTitle($title);
            $feed->setDescription($title);
        }
        
        if($fullUrl!='') $feed->setLink($fullUrl);
        
        if(empty($other_data["author"]["name"])) $other_data["author"]["name"] = " ";
        if(empty($other_data["author"]["email"])) $other_data["author"]["email"] = " ";
        if(empty($other_data["author"]["url"])) $other_data["author"]["url"] = " ";
            
        $feed->addAuthor($other_data["author"]);
        
        $feed->setDateModified(time());
        
        /**
        * Add one or more entries. Note that entries must
        * be manually added once created.
        */
        
        if(is_array($items) && count($items) > 0) {
            foreach($items as $item) {
                $entry = $feed->createEntry();
                
                $entry->setTitle($item->get_title());
                
                $entry->setLink($this->view->serverUrl().$this->view->url(array('module' => $other_data["url_params"]["module"],'controller' => $other_data["url_params"]["controller"], 'action' => $other_data["url_params"]["action"], 'url_id' => $item->get_url_id())));
                
                $publish_date = $item->get_data("publish_date");
                if($publish_date!='')
                    $entry->setDateModified(strtotime($item->get_data("publish_date")));
                else
                    $entry->setDateModified(strtotime($item->get_posted()));
                
                $entry->setDateCreated(HCMS_Utils_Time::timeMysql2Ts($item->get_posted()));
                
                $content = $item->get_content();
                if($content!='') $entry->setContent($content);
                
                $feed->addEntry($entry);
            }
         }        

        /**
        * Render the resulting feed to Atom 1.0 and assign to $out.
        * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
        */
        return $feed->export('rss');
    }
}
?>