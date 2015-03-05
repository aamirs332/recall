<?php
class Rcl_Options {
    public $key;
    
    function __construct($key=false){
        $this->key=get_key_addon_rcl(pathinfo($key));
    }
    
    function options($title,$conts){
        $return = '<span class="title-option">'.$title.'</span>		
	<div ';
        if($this->key) $return .= 'id="options-'.$this->key.'" ';
        $return .= 'class="wrap-recall-options">';
        if(is_array($conts)){
            foreach($conts as $content){
                $return .= $content;
            }
        }else{
            $return .= $conts;
        }
            $return .= '</div>';
        return $return;
    }
    
    function option_block($conts){
        $return = '<div class="option-block">';
        foreach($conts as $content){
            $return .= $content;
        }
        $return .= '</div>';
        return $return;
    }
    
    function child($args,$conts){
        $return = '<div class="child-select '.$args['name'].'" id="'.$args['name'].'-'.$args['value'].'">';
        foreach($conts as $content){
            $return .= $content;
        }
        $return .= '</div>';
        return $return;
    }
    
    function tab_name($name,$id=false){
        global $rcl_options;
        if(!$this->key) return false;
	$content = $this->label('Название вкладки в ЛК');
        if($rcl_options[$id]['tab_'.$this->key]) $rcl_options['tab_'.$this->key] = $rcl_options[$id]['tab_'.$this->key];
	elseif(!$rcl_options['tab_'.$this->key]) $rcl_options['tab_'.$this->key] = $name;	
        $content .= $this->option('text',array('name'=>'tab_'.$this->key));
        $content .= $this->notice('Впишите свою надпись на кнопке переключения вкладки в личном кабинете');
	return $content;
    }
    
    function title($title){
        return '<h3>'.$title.'</h3>';
    }
    
    function label($label){
        return '<label>'.$label.'</label>';
    }
    
    function notice($notice){
        return '<small>'.$notice.'</small>';
    }
    
    function option($type,$args){
        return $this->$type($args);
    }
    
    function select($args){
        global $rcl_options;
        
        if(isset($args['default'])&&!isset($rcl_options[$args['name']])) 
            $rcl_options[$args['name']] = $args['default'];
        
        $content = '<select ';
        if(isset($args['parent'])) $content .= 'class="parent-select" ';       
        $content .= 'name="'.$args['name'].'" size="1">';
            foreach($args['options'] as $val=>$name){
               $key = (isset($rcl_options[$args['name']]))? $rcl_options[$args['name']]:'';
               $content .= '<option value="'.$val.'" '.selected($key,$val,false).'>'
                       . $name
                       .'</option>';
            }
        $content .= '</select>';
        return $content;
    }
    
    function text($args){
        global $rcl_options;
        $val = (isset($rcl_options[$args['name']]))?$rcl_options[$args['name']]:'';
        return '<input type="text" name="'.$args['name'].'" value="'.$val.'" size="60">';
    }
    
    function number($args){
        global $rcl_options;
        $val = (isset($rcl_options[$args['name']]))?$rcl_options[$args['name']]:'';
        return '<input type="number" name="'.$args['name'].'" value="'.$val.'" size="60">';
    }
    
    function email($args){
        global $rcl_options;
        $val = (isset($rcl_options[$args['name']]))?$rcl_options[$args['name']]:'';
        return '<input type="email" name="'.$args['name'].'" value="'.$val.'" size="60">';
    }
    
    function url($args){
        global $rcl_options;
        $val = (isset($rcl_options[$args['name']]))?$rcl_options[$args['name']]:'';
        return '<input type="url" name="'.$args['name'].'" value="'.$val.'" size="60">';
    }
    
    function textarea($args){
        global $rcl_options;
        $val = (isset($rcl_options[$args['name']]))?$rcl_options[$args['name']]:'';
        return '<textarea name="'.$args['name'].'">'.$val.'</textarea>';
    }
}

function get_name_tab_rcl($name,$key){
	global $rcl_options;
	$content = '<label>Название вкладки в ЛК</label>';
	if(!$rcl_options['tab_'.$key]) $rcl_options['tab_'.$key] = $name;
	$content .= '<input type="text" name="tab_'.$key.'" value="'.$rcl_options['tab_'.$key].'" size="10">
	<small>Впишите свою надпись на кнопке переключения вкладки в личном кабинете</small>';
	return $content;
}
