<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailbox {

    public function getMailbox() {
        require_once(dirname(__FILE__) . '/ImapMailbox/ImapMailbox.php');

        define('ATTACHMENTS_DIR', dirname(__FILE__) . '/ImapMailbox/attachments');

        $mailbox = new ImapMailbox('{pop3.computeronsite.com:110/pop3/novalidate-cert}INBOX', 'workorders@astordashboard.com', 'Mike1', ATTACHMENTS_DIR, 'utf-8');
        //$mailbox = new ImapMailbox('{mail.softgrad.com:110/pop3/novalidate-cert}INBOX', 'demo-astor@softgrad.com', 'u8tzTEe7s1H8', ATTACHMENTS_DIR, 'utf-8');

        return $mailbox;
    }

}
