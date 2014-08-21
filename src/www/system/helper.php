<?php

namespace ZippyERP\System;

use \ZippyERP\System\User;

/**
 * Вспомагательный  класс  для  работы  с  бизнес-данными
 */
class Helper
{

    /**
     * Выполняет  логин  в  системму
     * 
     * @param mixed $login
     * @param mixed $password
     * @return  boolean 
     */
    public static function login($login, $password = null)
    {

        $user = User::findOne("  userlogin='{$login}' ");

        if ($user == null)
            return false;
        if ($user->userpass == $password)
            return $user;
        if (strlen($password) > 0) {
            $b = password_verify($password, $user->userpass);
            return $b ? $user : false;
        }
        return false;
    }

    /**
     * Проверка  существования логина
     * 
     * @param mixed $login
     */
    public static function existsLogin($login)
    {
        $list = \ZippyERP\System\User::find("  userlogin='{$login}' ");

        return count($list) > 0;
    }

    /*

      public static function _getUsersByRole($role_id)
      {
      return User::find('user_id  in (select user_id from system_user_role where role_id  = ' . $role_id . ')');
      }


      public static function _getRolesByUser($user_id)
      {
      return Role::find( 'user_id  in (select user_id from system_user_role where role_id  = ' . $role_id . ')');
      }
     */

    /**
     * Сохраняет  спписок  ролей для юзера
     * 
     */
    public static function saveRoles($user_id, $roles)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute('delete from system_user_role  where  user_id = ' . $user_id);
        foreach ($roles as $role_id) {
            $conn->Execute('insert into system_user_role (user_id,role_id) values(' . $user_id . ',' . $role_id . ')');
        }
    }

}
