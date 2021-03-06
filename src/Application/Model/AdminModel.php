<?php
/**
 * Administration Model Class
 *
 * All database functions regarding the Admin endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.7
 * @filesource
 */

namespace API\Model;

use API\Library;

class AdminModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

    public function generate_token($user, $token, $level)
    {
        try
        {
            $stmt = $this->_db->prepare("INSERT INTO auth_token (name, token, level) VALUES (:name, :token, :level)");
            $stmt->execute(
                [
                    ':name' => $user,
                    ':level' => $level,
                    ':token' => $token
                ]
            );

            if($stmt->rowCount() > 0)
            {
                $this->_output = true;
            } else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function auth_token($user, $token)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT level FROM auth_token WHERE name = :name AND token = :token");
            $stmt->execute(
                [
                    ':name' => $user,
                    ':token' => $token
                ]
            );

            $this->_output = ($stmt->rowCount() > 0) ? $stmt->fetch() : 0;
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function get_logs($log_level, $from, $to)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT * FROM logs WHERE date <= :to AND date >= :from AND err_level = $log_level");

            $stmt->execute(
                [
                    ':to' => $to,
                    ':from' => $from
                ]
            );

            $this->_output = ($stmt->rowCount() > 0) ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : 0;
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function add_api_user(array $user_details)
    {
        try
        {

            $stmt = $this->_db->prepare("INSERT INTO api_users (username, email, token, ip, last_access, SE_token) VALUES(:username, :email, :tid, :ip, NOW(), :setoken)");


            $stmt->execute(
                [
                    ':username' => $user_details['username'],
                    ':email' => $user_details['email'],
                    ':token' => $user_details['token'],

                    ':ip' => $user_details['ip'],
                    ':setoken' => (isset($user_details['SE_Token']) ? $user_details['SE_Token'] : 'na')

                ]
            );

            $this->_output = ($stmt->rowCount() > 0) ? $stmt->fetch() : 0;
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function revoke_token($user)
    {
        try
        {
            $stmt = $this->_db->prepare("DELETE FROM api_users WHERE username = :user");
            $stmt->execute(['user' => $user]);

            $stmt2 = $this->_db->prepare("DELETE FROM auth_token WHERE user = :user");
            $stmt2->execute(['user' => $user]);

            $this->_output = ($stmt->rowCount() > 0 && $stmt2->rowCount() > 0) ? true : false;
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}