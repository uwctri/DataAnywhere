<?php

namespace UWMadison\DataAnywhere;

use ExternalModules\AbstractExternalModule;
use REDCap;

class DataAnywhere extends AbstractExternalModule
{
    public function redcap_every_page_top($project_id)
    {
        // Custom Config page
        if ($this->isPage('ExternalModules/manager/project.php') && $project_id != NULL) {
            $url = $this->getUrl('config.js');
            echo "<script src={$url}></script>";
        }
    }

    public function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id)
    {
        if (empty($record)) return; // New Record, no data
        $settings = $this->getProjectSettings();
        $data = [];

        // Loop over all of the config, destination-all is arbitrary 
        foreach ($settings['destination-all'] as $index => $destAll) {
            $destAll   = $destAll == "1";
            $destEvent = $settings['destination-event'][$index];
            $destInst  = $settings['destination-instrument'][$index];

            if (
                $destAll || (($destEvent == $event_id) && ($destInst == $instrument)) ||
                (empty($destEvent) && ($destInst == $instrument)) || (($destEvent == $event_id) && empty($destInst))
            ) {
                $srcAll   = $settings['source-all'][$index] == "1";
                $srcEvent  = $settings['source-event'][$index];
                $srcInst   = $settings['source-instrument'][$index];
                $srcEvent = $srcAll ? null : $srcEvent;
                $fields = $srcAll ? null : REDCap::getFieldNames($srcInst);
                $tmp  = REDCap::getData($project_id, 'array', $record, $fields, $srcEvent);
                $data = array_replace_recursive($data, $tmp);
            }
        }

        // Pass data down to JS if we have any
        if (!empty($data)) {
            echo "<script> var DataAnywhere = " . json_encode([
                "data" => $data[$record],
                "eventNames" => REDCap::getEventNames(true),
                "usernames" => $this->getUsers()
            ]) . ";</script>";
        }
    }

    private function getUsers()
    {
        $users = "\"" . implode("\",\"", REDCap::getUsers()) . "\"";
        $sql = "SELECT username, CONCAT(user_firstname, ' ' , user_lastname) as name FROM redcap_user_information WHERE username in ($users)";
        $data = db_query($sql);
        $results = [];
        while ($row = db_fetch_assoc($data)) {
            $results[$row["username"]] = $row["name"];
        }
        return $results;
    }
}
