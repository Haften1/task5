<?php

namespace Only\Site\Handlers;

class Iblock
{
    public static function addLog($arFields)
    {
        $logIblockId = \Only\Site\Helpers\IBlock::getIblockID('LOG', 'CONTENT_RU');

        if ($arFields['IBLOCK_ID'] == $logIblockId) {
            return;
        }

        $iblockId = $arFields['IBLOCK_ID'];
        $elementId = $arFields['ID'];
        $arIblock = \CIBlock::GetByID($iblockId)->Fetch();
        $iblockName = $arIblock['NAME'];
        $parentSectionId = self::getSectionId($iblockId);

        if (!$parentSectionId) {
            $parentSectionId = self::createSection($iblockName, $logIblockId);
        }

        $logElementData = [
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $elementId,
            'ACTIVE' => 'Y',
            'ACTIVE_FROM' => ConvertTimeStamp(time(), "FULL"),
            'IBLOCK_SECTION_ID' => $parentSectionId,
            'PREVIEW_TEXT' => self::getDescription($iblockName, $elementId)
        ];

        $logElement = new \CIBlockElement();
        $logElementId = $logElement->Add($logElementData);

        return $logElementId ? true : false;
    }

    private static function getSectionId($iblockId)
    {
        $dbSection = \CIBlockSection::GetList([], ['IBLOCK_ID' => $iblockId], false, ['ID']);
        $arSection = $dbSection->Fetch();
        return $arSection ? $arSection['ID'] : null;
    }

    private static function createSection($iblockName, $logIblockId)
    {
        $section = new \CIBlockSection();
        $sectionData = [
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $iblockName,
            'ACTIVE' => 'Y'
        ];

        return $section->Add($sectionData);
    }

    private static function getDescription($iblockName, $elementId)
    {
        $path = self::getPath($iblockName, $elementId);
        return "{$iblockName} -> {$path} -> {$elementId}";
    }

    private static function getPath($iblockName, $elementId)
    {
        return $iblockName;
    }

    public static function OnAfterIBlockElementAddHandler(&$arFields)
    {
        self::addLog($arFields);
    }

    public static function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        self::addLog($arFields);
    }
}
