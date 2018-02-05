<?php

namespace ZippyERP\System;

/**
 * Содержит  вспомагательные функции-утилиты
 */
class Util
{

    /**
     * Возвращает  часть строки   в  юникоде
     *
     * @param mixed $str
     * @param mixed $count
     */
    public static function cutString($str, $count) {
        if (mb_strlen($str, 'UTF-8') <= $count) {
            return $str;
        }
        $str = mb_substr($str, 0, $count, 'UTF-8');
        $p = mb_strrpos($str, ' ', 0, 'UTF-8');
        return mb_substr($str, 0, $p, 'UTF-8');
    }

    /**
     * удаляет  рекурсивно  каталог
     *
     * @param mixed $dir
     */
    public static function removeDirRec($dir) {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? self::removeDirRec($obj) : unlink($obj);
            }
        }
        @rmdir($dir);
    }

    public static function createThumb($filepath, $size, $ext = 'jpg') {
        $imagedata = getimagesize($filepath);

        if (preg_match('/(gif|png|jpeg)$/', $imagedata['mime']) == 0) {

            return false;
        }
        if (preg_match('/(gif|png|jpg)$/', $ext) == 0) {

            return false;
        }
        $info = pathinfo($filepath);
        $thumb = $info['dirname'] . '/' . $info['filename'] . '_thmb.' . $ext;

        $imagewidth = $imagedata[0];
        $imageheight = $imagedata[1];

        $max = max($imagewidth, $imageheight);
        $percent = 1.0 * $size / $max;

        $new_width = $imagewidth * $percent;
        $new_height = $imageheight * $percent;

        $image_new = imagecreatetruecolor($new_width, $new_height);
        $image_old = imagecreatefromstring(file_get_contents($filepath));
        imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $new_width, $new_height, $imagewidth, $imageheight);
        switch ($ext) {
            case 'jpg':
                imagejpeg($image_new, $thumb);
                break;
            case 'png':
                imagepng($image_new, $thumb);
                break;
            case 'gif':
                imagegif($image_new, $thumb);
                break;
        }


        return true;
    }

}
