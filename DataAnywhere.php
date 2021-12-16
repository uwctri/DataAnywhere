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
            $this->includeJs('config.js');
        }
    }
    
    public function redcap_data_entry_form_top ( $project_id, $record, $instrument, $event_id) {
        if ( empty($record) )
            return; // New Record, no data
        $this->initGlobal();
        $settings = $this->getProjectSettings();
        $data = [];
        foreach ( $settings['destination-all']['value'] as $index => $destAll ) {
            $destAll   = $destAll == "1";
            $destEvent = $settings['destination-event']['value'][$index];
            $destInst  = $settings['destination-instrument']['value'][$index];
            
            if ( $destAll || ( ($destEvent == $event_id) && ($destInst == $instrument) ) || 
            ( empty($destEvent) && ($destInst == $instrument) ) || ( ($destEvent == $event_id) && empty($destInst) ) ) {
                $srcAll   = $settings['source-all']['value'][$index] == "1";
                $srcEvent  = $settings['source-event']['value'][$index];
                $srcInst   = $settings['source-instrument']['value'][$index];
                $srcEvent = $srcAll ? null : $srcEvent;
                $fields = $srcAll ? null : REDCap::getFieldNames($srcInst);
                $tmp  = REDCap::getData($project_id,'array',$record,$fields,$srcEvent);
                $data = $data + $tmp ? $tmp[$record] : [];
            }
        }
        if ( !empty($data) ) {
            $this->passArgument('data',$data);
            $this->passArgument('eventNames',REDCap::getEventNames(true));
        }
    }
    
    private function initGlobal() {
        $data = json_encode([
            "modulePrefix" => $this->getPrefix()
        ]);
        echo "<script>var {$this->module_global} = {$data};</script>";
    }
    
    private function passArgument($name, $value) {
        echo "<script>{$this->module_global}.{$name} = ".json_encode($value).";</script>";
    }
    
    private function includeJs($path) {
        echo "<script src={$this->getUrl($path)}></script>";
    }
    
}

?>
