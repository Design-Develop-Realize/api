<?php
/**
 * Questions Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Questions extends Library\BaseController
{
    private $_db;

    public function __construct()
    {
		parent::__construct();

		$this->_db = new Model\QuestionModel();
    }

    /**
     * Adds questions to the system
     *
     * @return array|string Output either confirming submission or returning an error
     * @throws \Exception
     */
    public function add()
    {
        $this->_log->set_message("Questions::add() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $channel  = $this->_params[0];
        $user     = $this->_params[1];
        $question = urldecode($this->_params[2]);

        if(count($this->_params) > 3)
        {
            for($i = 3; $i < count($this->_params); $i++)
            {
                $question .= "/" . $this->_params[$i];
            }
        }

        $this->_output->setOutput((isset($this->_params[3])) ? $this->_params[3] : NULL);

        //if question is filled in then user is!
        if($question != '')
        {
            $query = $this->_db->add_question($channel, $user, $question);

            return (!is_string($query) && $query == true) ? $this->_output->output(200, "Question Added", true) : $this->_output->output(400, $query);
        } else {
            $this->_log->set_message("URI is missing parameters, we have: $channel, $user, $question", "WARNING");

            return $this->_output->output(400, "You forgot to actually ask a question!", true);
        }
    }

    /**
     * Adds questions to the system with no reply
     *
     * @throws \Exception
     */
    public function add_empty()
    {
        $this->_log->set_message("Questions::add+empty() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $channel  = $this->_params[0];
        $user     = $this->_params[1];
        $question = urldecode($this->_params[2]);

        if(count($this->_params) > 3)
        {
            for($i = 3; $i < count($this->_params); $i++)
            {
                $question .= "/" . $this->_params[$i];
            }
        }

        $this->_output->setOutput((isset($this->_params[3])) ? $this->_params[3] : NULL);

        //if question is filled in then user is!
        if($question != '')
        {
            $query = $this->_db->add_question($channel, $user, $question);

            return (!is_string($query) && $query == true) ? $this->_output->output(200, "", true) : $this->_output->output(400, $query);
        } else {
            $this->_log->set_message("URI is missing parameters, we have: $channel, $user, $question", "WARNING");

            return $this->_output->output(400, "You forgot to actually ask a question!", true);
        }
    }

    /**
     * Marks a question as read
     *
     * @return array|string Output either confirming question marked as read or an error
     * @throws \Exception
     */
    public function read()
    {
        $this->_log->set_message("Questions::read() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $qid = $this->_params[0];

        $this->_output->setOutput((isset($this->_params[1])) ? $this->_params[1] : NULL);

        $query = $this->_db->mark_read($qid);

        if($query)
        {
            return $this->_output->output(200, "Question is marked as read", false);
        } else {
            $this->_log->set_message("Something went wrong", "WARNING");

            return $this->_output->output(400, "OOPS! There was an error", false);
        }
    }

    /**
     * Reutrns all questions in the queue for the current user that was submitted in the past 4 hours
     *
     * @return array|string The output of the questions
     * @throws \Exception
     */
    public function showlist()
    {
        $this->_log->set_message("Questions::showlist() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $chan = $this->_params[0];
        $bot = (isset($this->_params[1])) ? $this->_params[1] : false;

        $this->_output->setOutput((isset($this->_params[2])) ? $this->_params[2] : NULL);

        $query = $this->_db->list_questions($chan);

        //lets actually check we have results!
        if(is_array($query))
        {
            return $this->_output->output(200, $query, $bot);
        } else {
            return $this->_output->output(200, "There are currently no questions", $bot);
        }
    }
}