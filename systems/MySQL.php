<?php

namespace Systems;

use Systems\Lib\QueryWrapper;

class MySQL extends QueryWrapper
{
    protected static $db;
}

MySQL::connect("mysql:host=".DBHOSTSIMPEG.";port=".DBPORTSIMPEG.";dbname=".DBNAMESIMPEG."", DBUSERSIMPEG, DBPASSSIMPEG);
