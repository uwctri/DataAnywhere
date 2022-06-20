<?php

namespace UWMadison\DataAnywhere;
use ExternalModules\AbstractExternalModule;
use REDCap;

class DataAnywhere extends AbstractExternalModule {
    
    private $module_global = 'DataAnywhere';
    
    public function redcap_every_page_top($project_id) {
        // Custom Config page
        if ( $this->isPage('ExternalModules/manager/project.php') && $project_id != NULL) {
            $this->initGlobal();
            $url = $this->getUrl('config.js');
            echo "<script src={$url}></script>";
        }
    }
    
    public function redcap_data_entry_form_top ( $project_id, $record, $instrument, $event_id) {
        if ( empty($record) )
            return; // New Record, no data
        $this->initGlobal();
        $settings = $this->getProjectSettings();
        $data = [];
        
        // Loop over all of the config, destination-all is arbitrary 
        foreach ( $settings['destination-all'] as $index => $destAll ) {
            $destAll   = $destAll == "1";
            $destEvent = $settings['destination-event'][$index];
            $destInst  = $settings['destination-instrument'][$index];
            
            if ( $destAll || ( ($destEvent == $event_id) && ($destInst == $instrument) ) || 
            ( empty($destEvent) && ($destInst == $instrument) ) || ( ($destEvent == $event_id) && empty($destInst) ) ) {
                $srcAll   = $settings['source-all'][$index] == "1";
                $srcEvent  = $settings['source-event'][$index];
                $srcInst   = $settings['source-instrument'][$index];
                $srcEvent = $srcAll ? null : $srcEvent;
                $fields = $srcAll ? null : REDCap::getFieldNames($srcInst);
                $tmp  = REDCap::getData($project_id,'array',$record,$fields,$srcEvent);
                $data = array_replace_recursive($data, $tmp);
            }
        }
        
        // Post data down to JS if we have any
        if ( !empty($data) ) {
            echo "<script>{$this->module_global}.data = ".json_encode($data[$record]).";</script>";
            echo "<script>{$this->module_global}.eventNames = ".json_encode(REDCap::getEventNames(true)).";</script>";
        }
    }
    
    private function initGlobal() {
        $data = json_encode([
            "modulePrefix" => $this->getPrefix()
        ]);
        echo "<script>var {$this->module_global} = {$data};</script>";
    }
}

?>
