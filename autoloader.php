<?php

function loader($class)
{
    include $class . '.php';
}

spl_autoload_register('loader');