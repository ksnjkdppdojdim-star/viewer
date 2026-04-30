<?php
namespace Core\Events\Events;

use Core\Events\Event;

class UserEvent extends Event
{
    const BEFORE_LOGIN    = 'user.before_login';
    const LOGIN_SUCCESS   = 'user.login.success';
    const LOGIN_FAILED    = 'user.login.failed';
    const LOGOUT          = 'user.logout';
    
    const BEFORE_REGISTER = 'user.before_register';
    const AFTER_REGISTER  = 'user.after_register';
    
    const PROFILE_UPDATE  = 'user.profile_update';
    const PASSWORD_CHANGE = 'user.password_change';
    const PERMISSION_DENIED = 'user.permission_denied';

    public function __construct(string $name, $user = null, array $params = [])
    {
        parent::__construct($name, array_merge(['user' => $user], $params));
    }
    
    public function getUser()
    {
        return $this->getParam('user');
    }
}
