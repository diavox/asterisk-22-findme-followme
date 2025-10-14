<?php
include('autoloader.php');

$pub = new Publisher();

echo $pub->title . PHP_EOL;

//print_r($pub->parseConfig("env.ini"));

$config = $pub->parseConfig("env.ini");

echo "Environment is set to " . $config['ENVIRONMENT'] . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

$environment = strtoupper($config['ENVIRONMENT']);
$firewallStop = $config[$environment]['FIREWALL_STOP'];

/* AGI-BIN */
//echo "Load agi-bin files." . PHP_EOL;
$src = __DIR__ . '//agi//';
$dst = $config[$environment]['PATH_AGI'];
//echo "Publishing agi-bin files..." . PHP_EOL;
$pub->copy_folder($src, $dst);
echo "Setting up permissions!" . PHP_EOL;
$pub->set777Permission($environment, $dst);
echo "agi-bin files published successfully!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;
/* CONFIG */
//echo "Load configuration files." . PHP_EOL;
$src = __DIR__ . '//config//';
$dst = $config[$environment]['PATH_CONFIG'];
//echo "Publishing configuration files..." . PHP_EOL;
$pub->copy_folder($src, $dst);
echo "configuration files published successfully!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;
/* DIALPLAN */
//echo "Load dialplan files." . PHP_EOL;
$src = __DIR__ . '//dialplan//';
$dst = $config[$environment]['PATH_DIALPLAN'];
//echo "Publishing dialplan files..." . PHP_EOL;
$pub->copy_folder($src, $dst);
echo "dialplan files published successfully!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;
/* SIP */
//echo "Load sip files." . PHP_EOL;
$src = __DIR__ . '//sip//';
$dst = $config[$environment]['PATH_SIP'];
//echo "Publishing sip files..." . PHP_EOL;
$pub->copy_folder($src, $dst);
echo "sip files published successfully!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;
/* SOUNDS */
//echo "Load sounds files." . PHP_EOL;
$src = __DIR__ . '//sounds//';
$dst = $config[$environment]['PATH_SOUNDS'];
//echo "Publishing sounds files..." . PHP_EOL;
$pub->copy_folder($src, $dst);
echo "sound files published successfully!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

// FILE and FOLDER PERMISSION
/* VAR LOG */
$usergroup = $config[$environment]['USERGROUP_APACHE'];
$dst = $config[$environment]['PATH_RISE_LOG'];
$pub->setUserGroupPermission($environment, $dst, $usergroup);
echo "Setting up permissions for usergroup [" . $usergroup . "]!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

$usergroup = $config[$environment]['USERGROUP_ASTERISK'];
$dst = $config[$environment]['PATH_RISEIVR_LOG'];
$pub->setUserGroupPermission($environment, $dst, $usergroup);
echo "Setting up permissions for usergroup [" . $usergroup . "]!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

// FILE and FOLDER PERMISSION
/* path sounds greetings */
$usergroup = $config[$environment]['USERGROUP_ASTERISK'];
$dst = $config[$environment]['PATH_SOUNDS']."greetings/";
$pub->setUserGroupPermission($environment, $dst, $usergroup);
echo "Setting up permissions to $dst for usergroup [" . $usergroup . "]!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

// FILE and FOLDER PERMISSION
/* path sounds messages */
$usergroup = $config[$environment]['USERGROUP_ASTERISK'];
$dst = $config[$environment]['PATH_SOUNDS']."messages/";
$pub->setUserGroupPermission($environment, $dst, $usergroup);
echo "Setting up permissions to $dst for usergroup [" . $usergroup . "]!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

// FILE and FOLDER PERMISSION
/* path sounds names */
$usergroup = $config[$environment]['USERGROUP_ASTERISK'];
$dst = $config[$environment]['PATH_SOUNDS']."names/";
$pub->setUserGroupPermission($environment, $dst, $usergroup);
echo "Setting up permissions to $dst for usergroup [" . $usergroup . "]!" . PHP_EOL;
echo "<<<< --------------------------- >>>>" . PHP_EOL;

// DISABLE FIREWALL FOR TESTING
echo "Firewall Stop = " . $firewallStop . PHP_EOL;
$pub->disableFirewall($environment, $firewallStop);
// ASTERISK DIALPLAN RELOAD
$pub->reloadSIP($environment);
$pub->reloadDialplan($environment);
