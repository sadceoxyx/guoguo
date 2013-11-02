<?php
class ContentRotator{
    
    
    static function parse($tpl, $hash){
        foreach($hash as $key => $value){
            $tpl = str_replace('[+'.$key.'+]', $value, $tpl);
        }
        return $tpl;
    }
}
//EOF
?>
