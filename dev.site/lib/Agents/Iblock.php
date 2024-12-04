<?php

namespace Only\Site\Agents;

class Iblock
{
    public static function clearOldLogs()
    {
        global $DB;

        try {
            if (\Bitrix\Main\Loader::includeModule('iblock')) {
                $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');

                if (!$iblockId) {
                    throw new \Exception("Iblock ID not found for QUARRIES_SEARCH in SYSTEM.");
                }

                $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));

                $rsLogs = \CIBlockElement::GetList(
                    ['TIMESTAMP_X' => 'DESC'],
                    ['IBLOCK_ID' => $iblockId],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID']
                );

                $logsToDelete = [];
                $counter = 0;

                while ($arLog = $rsLogs->Fetch()) {
                    $counter++;
                    if ($counter > 10) {
                        $logsToDelete[] = $arLog['ID'];
                    }
                }

                foreach ($logsToDelete as $logId) {
                    $result = \CIBlockElement::Delete($logId);
                    if (!$result) {
                        error_log("Failed to delete element with ID: " . $logId);
                    }
                }

                return "Old logs cleared, except for the latest 10.";
            } else {
                throw new \Exception("Iblock module is not installed.");
            }
        } catch (\Exception $e) {
            error_log("Error in " . __CLASS__ . "::" . __FUNCTION__ . ": " . $e->getMessage());
            return "Error occurred during log clearing.";
        }
    }
}
