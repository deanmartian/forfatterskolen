<?php

namespace App\Http;

class EmailReader
{
    // imap server connection
    public $conn;

    // inbox storage and inbox message count
    private $inbox;

    private $msg_cnt;

    // email login credentials
    private $server = 'imap.domeneshop.no';

    private $user = '';

    private $pass = '';

    private $port = 993; // adjust according to server settings

    // connect to the server and get the inbox emails
    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
        /*$this->connect();
        $this->inbox();*/
    }

    // close the server connection
    public function close()
    {
        $this->inbox = [];
        $this->msg_cnt = 0;

        imap_close($this->conn);
    }

    // open the server connection
    // the imap_open function parameters will need to be changed for the particular server
    // these are laid out to connect to a Dreamhost IMAP server
    public function connect()
    {
        $this->conn = @imap_open('{'.$this->server.'}', $this->user, $this->pass);

        if (is_array(imap_errors())) {
            return false;
        }

        return true;
    }

    // move the message to a new folder
    public function move($msg_index, $folder = 'INBOX.Processed')
    {
        // move on server
        imap_mail_move($this->conn, $msg_index, $folder);
        imap_expunge($this->conn);

        // re-read the inbox
        $this->inbox();
    }

    // get a specific message (1 = first email, 2 = second email, etc.)
    public function get($msg_index = null)
    {
        $index = $msg_index - 1;

        /*if (count($this->inbox) <= 0) {
            return array();
        }
        elseif ( ! is_null($msg_index) && isset($this->inbox[$index])) {
            return $this->inbox[$index];
        }*/

        return $this->inbox()[$index]; // $this->inbox[0];
    }

    public function delete($msg_index = null)
    {
        if (count($this->inbox()) <= 0) {
            return false;
        } elseif (! is_null($msg_index)) {
            imap_delete($this->conn, $msg_index);
            imap_expunge($this->conn);

            return true;
        }

        return false;
    }

    // read the inbox

    /**
     * For imap_fetchbody
     * (empty) - Entire message
     * 0 - Message header
     * 1 - MULTIPART/ALTERNATIVE
     * 1.1 - TEXT/PLAIN
     * 1.2 - TEXT/HTML
     * 2 - file.ext
     */
    public function inbox(): array
    {
        ini_set('memory_limit', '160M');
        if ($this->connect()) {
            $this->msg_cnt = imap_num_msg($this->conn);

            $in = [];
            for ($i = 1; $i <= $this->msg_cnt; $i++) {
                $readable_body = imap_qprint(imap_fetchbody($this->conn, $i, 1.2));
                if (empty($readable_body)) {
                    $readable_body = imap_qprint(imap_fetchbody($this->conn, $i, 1));
                }

                $in[] = [
                    'index' => $i,
                    'header' => imap_headerinfo($this->conn, $i),
                    'body' => imap_body($this->conn, $i),
                    'readable_body' => $readable_body,
                    'structure' => imap_fetchstructure($this->conn, $i),
                ];
            }

            return $this->inbox = $in;
        }
    }

    /**
     * Imap connection
     *
     * @return resource
     */
    public function connection()
    {
        return imap_open('{'.$this->server.'}', $this->user, $this->pass);
    }
}
