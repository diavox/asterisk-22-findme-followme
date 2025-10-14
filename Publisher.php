<?php

class Publisher
{
    public $title = "Welcome to Diavox Voicemail IVR Publisher...";
    public array $file_exlude = ['diavox-config.json'];

    public function parseConfig($config_file)
    {
        return parse_ini_file($config_file, true);
    }

    public function copy_folder($src, $dst)
    {
        // open the source directory
        $dir = opendir($src);

        // Make the destination directory if not exist
        @mkdir($dst);

        // Loop through the files in source directory
        foreach (scandir($src) as $file) {
            // If a file is found in file_exclude, skip copying and overwriting.
            if (in_array($file, $this->file_exlude)) continue;

            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {

                    // Recursively calling custom copy function
                    // for sub directory 
                    $this->copy_folder($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function disableFirewall($envi, $firewallStop)
    {
        if (strtoupper($envi) == "LINUX" && $firewallStop) {
            exec('systemctl stop firewalld');
            echo "firewall has been stopped for testing purposes!" . PHP_EOL;
        } else {
            echo "skipped firewall disabling..." . PHP_EOL;
        }
    }

    public function reloadDialplan($envi)
    {
        if ($envi == "LINUX") {
            exec('asterisk -rx "dialplan reload"');
            echo "dialplan reloaded!" . PHP_EOL;
        } else {
            echo "skipped dialplan reload..." . PHP_EOL;
        }
    }

    public function reloadSIP($envi)
    {
        if (strtoupper($envi) == "LINUX") {
            exec('asterisk -rx "pjsip reload"');
            echo "pjsip reloaded!" . PHP_EOL;
        } else {
            echo "skipped pjsip reload..." . PHP_EOL;
        }
    }

    public function reloadConfBridge($envi)
    {
        if (strtoupper($envi) == "LINUX") {
            exec('asterisk -rx "module reload app_confbridge.so"');
            echo "confbridge reloaded!" . PHP_EOL;
        } else {
            echo "skipped confbridge reload..." . PHP_EOL;
        }
    }

    public function reloadFeatures($envi)
    {
        if (strtoupper($envi) == "LINUX") {
            exec('asterisk -rx "module reload features"');
            echo "features reloaded!" . PHP_EOL;
        } else {
            echo "skipped features reload..." . PHP_EOL;
        }
    }

    public function set755Permission($envi, $path)
    {
        if (strtoupper($envi) == "LINUX") {
            exec('chmod -R 755 ' . $path);
            echo "dialplan reloaded!" . PHP_EOL;
        } else {
            echo "skipped dialplan reload..." . PHP_EOL;
        }
    }

    public function set777Permission($envi, $path)
    {
        if (strtoupper($envi) == strtoupper("Linux")) {
            echo "Executing: chmod -R 777 " . $path . PHP_EOL;
            exec('chmod -R 777 ' . $path);
            echo "Permission applied!" . PHP_EOL;
        } else {
            echo "skipped permission..." . PHP_EOL;
        }
    }

    public function setUserGroupPermission($envi, $path, $user)
    {
        if (strtoupper($envi) == strtoupper("Linux")) {
            echo "Executing: chown -R " . $user . ":" . $user . " " . $path . PHP_EOL;
            exec("chown -R " . $user . ":" . $user . " " . $path);
            echo "Permission applied!" . PHP_EOL;
        } else {
            echo "skipped permission..." . PHP_EOL;
        }
    }
}
