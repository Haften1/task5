<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

CModule::AddAutoloadClasses(
    'dev.site', 
    [
        'Only\\Site\\Handlers\\Iblock' => 'lib/Handlers/Iblock.php',
        'Only\\Site\\Agents\\Iblock' => 'lib/Agents/Iblock.php',
    ]
);

RegisterModule('dev.site');
