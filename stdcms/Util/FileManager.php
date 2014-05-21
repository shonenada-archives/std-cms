<?php

namespace Util;

class FileManager {

    static public function manager($dir_name='', $path='', $order='') {
        $root_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/uploads/';
        $root_path = realpath($root_path) . '/';
        $root_url = substr($_SERVER['SCRIPT_NAME'], 0, -9) . 'uploads/';

        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            return false;
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
        }           
        if (empty($path)) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $path;
            $current_url = $root_url . $path;
            $current_dir_path = $path;
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        $order = empty($order) ? 'name' : strtolower($order);

        if (preg_match('/\.\./', $current_path)) {
            return false;
        }

        if (!preg_match('/\/$/', $current_path)) {
            return false;
        }

        if (!file_exists($current_path) || !is_dir($current_path)) {
            return false;
        }

        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') continue;
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true;
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2);
                    $file_list[$i]['filesize'] = 0;
                    $file_list[$i]['is_photo'] = false;
                    $file_list[$i]['filetype'] = '';
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $temp_arr = explode('.', trim($filename));
                    $file_ext = strtolower(array_pop($temp_arr));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename;
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file));
                $i++;
            }
            closedir($handle);
        }

        usort($file_list, function ($one, $two) {
            global $order;
            if ($one['is_dir'] && !$two['is_dir']) {
                return -1;
            } else if (!$one['is_dir'] && $two['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($one['filesize'] > $two['filesize']) {
                        return 1;
                    } else if ($one['filesize'] < $two['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($one['filetype'], $two['filetype']);
                } else {
                    return strcmp($one['filename'], $two['filename']);
                }
            }
        });

        $result = array();
        $result['moveup_dir_path'] = $moveup_dir_path;
        $result['current_dir_path'] = $current_dir_path;
        $result['current_url'] = $current_url;
        $result['total_count'] = count($file_list);
        $result['file_list'] = $file_list;

        return $result;
    }

}
