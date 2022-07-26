<?php

namespace core;

use core\helpers\GenerateToken;
use Exception;

class Session
{
    public function __construct()
    {
        session_start();
    }

    public static function exists($name): bool
    {
        return isset($_SESSION[$name]);
    }

    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function get($name)
    {
        if (self::exists($name) && !empty($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return false;
    }

    public static function delete($name)
    {
        unset($_SESSION[$name]);
    }


    /**
     * @throws Exception
     */
    public static function createCsrfToken()
    {
        $token = GenerateToken::createToken();
        self::set('_token', $token);
        return $token;
    }

    /**
     * @throws Exception
     */
    public static function csrfCheck()
    {
        $request = new Request();
        $check = $request->get('_token');
        if (self::exists('_token') && self::get('_token') == $check) {
            return true;
        }
        throw new Exception(Errors::get('3000'), 3000);
    }

    // $type can be primary, secondary, success, danger, warning, info, light, dark
    public static function msg($msg, $type = 'danger')
    {
        $alerts = self::exists('session_alerts') ? self::get('session_alerts') : [];
        $alerts[$type][] = $msg;
        self::set('session_alerts', $alerts);
    }

    public static function displaySessionAlerts()
    {
        $alerts = self::exists('session_alerts') ? self::get('session_alerts') : [];
        $html = "";
        foreach ($alerts as $type => $msgs) {
            foreach ($msgs as $msg) {
                $html .= "<div class='alert alert-{$type} alert-dismissible fade show mt-3 mx-2 shadow-lg fixed-top' role='alert' style='z-index: 5000;' id='clearAlert'>
                {$msg}
          <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
            }
        }
        self::delete('session_alerts');
        return $html;
    }
}