<?php

namespace ZippyERP\System;

use ZCL\DB\Entity;

/**
 *  Класс  инкапсулирующий   сущность  User
 * @table=system_users
 * @keyfield=user_id
 * @view_=system_users_view
 */
class User extends Entity
{

    private $roles = null;

    /**
     * @see Entity
     *
     */
    protected function init()
    {
        $this->userlogin = "Guest";
        $this->user_id = 0;
        $this->registration_date = date('Y-m-d', time());
    }

    /**
     * Проверка  залогинивания
     *
     */
    public function isLogined()
    {
        return $this->user_id > 0;
    }

    /**
     * Выход из  системмы
     *
     */
    public function logout()
    {
        $this->init();
    }

    /**
     * @see Entity
     *
     */
    protected function afterLoad()
    {
        $this->registration_date = strtotime($this->registration_date);
    }

    /**
     * @see Entity
     *
     */
    protected function beforeSave()
    {
        if ($this->user_id == 0) {
            //    mkdir(_ROOT .UPLOAD_USERS . $this->userlogin .'/');
        }
    }

    /**
     * @see Entity
     *
     */
    protected function beforeDelete()
    {
        if ($objs = glob(_ROOT . UPLOAD_USERS . $this->user_id . "/*")) {
            foreach ($objs as $obj) {
                unlink($obj);
            }
        }
        @rmdir(_ROOT . UPLOAD_USERS . $this->userlogin . '/');
    }

    /**
     * Возвращает  пользователя   по  логину
     *
     * @param mixed $login
     */
    public static function getByLogin($login)
    {
        $conn = \ZDB\DB::getConnect();
        return User::getFirst('userlogin = ' . $conn->qstr($login));
    }

    /**
     * Принадлежность к роли
     *
     * @param mixed $rolename
     */
    public function hasRole($rolename)
    {

        if ($this->roles == null) {
            $this->roles = array();
            $_roles = Role::find(' role_id in (select role_id from system_user_role where user_id = ' . $this->user_id . ')');

            foreach ($_roles as $_role) {
                $this->roles[] = $_role->rolename;
            }
        }
        return @in_array($rolename, $this->roles);
    }

    /**
     * Возвращает  пользователя   по  хешу
     *
     * @param mixed $md5hash
     */
    public static function getByHash($md5hash)
    {
        //$conn = \ZDB\DB::getConnect();
        $arr = User::find('md5hash=' . Entity::qstr($md5hash));
        if (count($arr) == 0) {
            return null;
        }
        $arr = array_values($arr);
        return $arr[0];
    }

    /**
     * Возвращает ID  пользователя
     *
     */
    public function getUserID()
    {
        return $this->user_id;
    }

}
