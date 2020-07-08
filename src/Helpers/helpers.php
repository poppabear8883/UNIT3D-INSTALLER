<?php

use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Yaml\Yaml;

if (!function_exists('installed')) {
    function installed($name)
    {
        if (shell_exec("command -v $name") != '') {
            return true;
        }

        return false;
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return __DIR__ . "/../../$path";
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '')
    {
        return __DIR__ . "/../Resources/$path";
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_random')) {
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     * @return string
     */
    function str_random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}

if (!function_exists('fqdn')) {
    function fqdn()
    {
        return trim(exec('hostname -f'));
    }
}

if (!function_exists('hostname')) {
    function hostname()
    {
        return trim(exec('hostname'));
    }
}

if (!function_exists('ip')) {
    function ip()
    {
        return trim(explode(' ', exec('hostname -I'))[0]);
    }
}

if (!function_exists('dd')) {
    function dd($var)
    {
        VarDumper::dump($var);
        die();
    }
}

if (!function_exists('array_find')) {
    function array_find($array, $searchKey = '')
    {
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
}

if (!function_exists('distinfo')) {
    function distinfo()
    {
        $distname = strtolower(trim(shell_exec('head -n1 /etc/issue | cut -f 1 -d \' \'')));
        $distver = trim(shell_exec('head -n1 /etc/issue | cut -f 2 -d \' \''));
        $lts = (trim(shell_exec('head -n1 /etc/issue | cut -f 3 -d \' \'') === 'LTS'));

        preg_match("/^[0-9]..[0-9]./m", $distver, $matches);
        $mainver = $matches[0];

        switch ($mainver) {
            case "20.04":
                $relname = "(Focal Fossa)";
                break;
            case "18.04":
                $relname = "(Bionic Beaver)";
                break;
            case "16.10":
                $relname = "(Yakkety Yak)";
                break;
            case "16.04":
                $relname = "(Xenial Xerus)";
                break;
            case "15.10":
                $relname = "(Wily Werewolf)";
                break;
            case "15.04":
                $relname = "(Vivid Vervet)";
                break;
            case "14.10":
                $relname = "(Utopic Unicorn)";
                break;
            case "14.04":
                $relname = "(Trusty Tahr)";
                break;
            case "13.10":
                $relname = "(Saucy Salamander)";
                break;
            case "13.04":
                $relname = "(Raring Ringtail)";
                break;
            case "12.10":
                $relname = "(Quantal Quetzal)";
                break;
            case "12.04":
                $relname = "(Precise Pangolin)";
                break;
            case "11.10":
                $relname = "(Oneiric Ocelot)";
                break;
            case "11.14":
                $relname = "(Natty Narwhal)";
                break;
            case "10.10":
                $relname = "(Maverick Meerkat)";
                break;
            case "10.04":
                $relname = "(Lucid Lynx)";
                break;
            case "9.10":
                $relname = "(Karmic Koala)";
                break;
            case "9.04":
                $relname = "(Jaunty Jackpole)";
                break;
            case "8.10":
                $relname = "(Intrepid Ibex)";
                break;
            case "8.04":
                $relname = "(Hardy Heron)";
                break;
            case "7.10":
                $relname = "(Gutsy Gibbon)";
                break;
            case "7.04":
                $relname = "(Feisty Fawn)";
                break;
            case "6.10":
                $relname = "(Edgy Eft)";
                break;
            case "6.06":
                $relname = "(Dapper Drake)";
                break;
            case "5.10":
                $relname = "(Breezy Badger)";
                break;
            case "5.04":
                $relname = "(Hoary Hedgehog)";
                break;
            case "4.10":
                $relname = "(Warty Warthog)";
                break;
            default:
                $relname = "UNKNOWN";
        }

        return array(
            'name' => $distname,
            'version' => $distver,
            'mainver' => $mainver,
            'relname' => $relname,
            'lts' => $lts
        );
    }
}

if (!function_exists('distname')) {
    function distname()
    {
        return strtolower(distinfo()['name']);
    }
}

if (!function_exists('distversion')) {
    function distversion()
    {
        return strtolower(distinfo()['version']);
    }
}

if (!function_exists('distmainver')) {
    function distmainver()
    {
        return strtolower(distinfo()['mainver']);
    }
}

if (!function_exists('distrelname')) {
    function distrelname()
    {
        return strtolower(distinfo()['name']);
    }
}

if (!function_exists('distlts')) {
    function distlts()
    {
        return strtolower(distinfo()['lts']);
    }
}

if (!function_exists('memory')) {
    function memory()
    {
        return shell_exec("grep 'MemTotal' /proc/meminfo |tr ' ' '\n' |grep [0-9]") != '';
    }
}