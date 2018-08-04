<?php

namespace App\Classes;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Config
{

    /**
     * @var array
     */
    protected $values = [];

    public function __construct()
    {
        $this->build();
    }

    public function get($path, $default = null)
    {
        $array = $this->values;

        if (!empty($path)) {
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }

    public function set($path, $value)
    {
        if (!empty($path)) {
            $at = &$this->values;
            $keys = explode('.', $path);

            while (count($keys) > 0) {
                if (count($keys) === 1) {
                    if (is_array($at)) {
                        $at[array_shift($keys)] = $value;
                    } else {
                        throw new \RuntimeException("Can not set value at this path ($path) because is not array.");
                    }
                } else {
                    $key = array_shift($keys);

                    if (!isset($at[$key])) {
                        $at[$key] = array();
                    }

                    $at = &$at[$key];
                }
            }
        } else {
            $this->values = $value;
        }

        return $value;
    }

    public function app($path, $value = null)
    {
        $path = "app.$path";
        return $value ? $this->set($path, $value) : $this->get($path);
    }

    public function os($path, $value = null)
    {
        $path = 'os.' . distname() . '.' . $path;
        return $value ? $this->set($path, $value) : $this->get($path);
    }

    public function add($path, array $values)
    {
        $get = (array)$this->get($path);
        $this->set($path, $this->arrayMergeRecursiveDistinct($get, $values));
    }

    public function have($path)
    {
        $keys = explode('.', $path);
        $array = $this->values;
        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    public function setValues($values)
    {
        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function search($searchKey, $array = null)
    {
        if ($array == null) {
            $array = $this->values;
        }

        //create a recursive iterator to loop over the array recursively
        $iter = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($array),
            RecursiveIteratorIterator::SELF_FIRST);

        //loop over the iterator
        foreach ($iter as $key => $value) {
            //if the key matches our search
            if ($key === $searchKey) {
                //add the current key
                $keys = array($key);
                //loop up the recursive chain
                for ($i = $iter->getDepth() - 1; $i >= 0; $i--) {
                    //add each parent key
                    array_unshift($keys, $iter->getSubIterator($i)->key());
                }
                //return our output array
                return array('path' => implode('.', $keys), 'value' => $value);
            }
        }

        //return false if not found
        return false;
    }

    protected function arrayMergeRecursiveDistinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                if (is_int($key)) {
                    $merged[] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                } else {
                    $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                }
            } else {
                if (is_int($key)) {
                    $merged[] = $value;
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    protected function build()
    {
        $directory = __DIR__ . '/../Configs';

        $files = scandir($directory);

        foreach ($files as $file) {
            if (is_file("$directory/$file")) {
                $conf = require $directory . "/$file";
                $this->values[basename($file, '.php')] = $conf;
            }
        }
    }

}