<?php

/**
 * Sample plugin to try out some hooks.
 * This performs an automatic login if accessed from localhost
 */
 
class test extends rcube_plugin{
    public $task='mail';

    function init(){     
       $this->register_handler('plugin.unreadmessage',array($this, 'unreadmessage_handler'));
    }
    function unreadmessage_handler(){
       $rcmail = rcmail::get_instance();        
       $count=$rcmail->imap->messagecount('INBOX', 'UNSEEN');
       return $count    
    }
}