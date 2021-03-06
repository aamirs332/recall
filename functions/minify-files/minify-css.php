<?php
class Rcl_Minify {
    public $id;
    public $path;
    
    function __construct($id,$path){
        $this->id = $id;
        $this->path = $path;
        if (!is_admin()) add_action('wp_enqueue_scripts', array(&$this,'output_style'));
        if (is_admin()) add_filter('csspath_array_rcl', array(&$this,'minify_css'));
    }
    
    function output_style(){
            global $rcl_options;	
            if(isset($rcl_options['minify_css'])&&$rcl_options['minify_css']==1) return;
            wp_enqueue_style( $this->id, addon_url('style.css', $this->path) );	
    }
    
    function minify_css($array){
            global $rcl_options;	
            if($rcl_options['minify_css']!=1) return;
            $path = pathinfo($this->path);
            $array[] = $path['dirname'].'/style.css';
            return $array;
    }
}

//подключаем стилевой файл дополнения
function rcl_enqueue_style($id,$path){
    $tab = new Rcl_Minify($id,$path);  
}

function minify_style_rcl(){
    global $rcl_options;	
    if(!isset($rcl_options['minify_css'])||$rcl_options['minify_css']!=1) return false;

    $css_dir = RCL_PATH.'css/';
    $css_ar = array(
        $css_dir.'lk.css',
        $css_dir.'recbar.css',
        $css_dir.'regform.css',
        $css_dir.'slider.css',
        $css_dir.'users.css',
        $css_dir.'style.css'
    );

    $csses = apply_filters('csspath_array_rcl',$css_ar);
    $path = TEMP_PATH.'css/';
    if(!is_dir($path)){
            mkdir($path);
            chmod($path, 0755);
    }
    $filename = 'minify.css';
    $file_src = $path.$filename;		
    $f = fopen($file_src, 'w');

    $fullcss = '';

    foreach($csses as $k=>$css_path){
        
        $url = '';
        $imgs = array();
        $us = array();
        if(!file_exists($css_path)) continue;
        $string_value = '';
        preg_match_all("/(?<=\/wp\-content\/)[A-z0-9\-\/\.\_\s\ё]*(?=)/i", $css_path, $string_value);
        if($k!==0) $fullcss .= "\n\n";
        $fullcss .= '/*'.$string_value[0][0].'*/'."\r\n";	
        $string = file_get_contents($css_path);
        if(addon_path($css_path)){           
            preg_match_all('/(?<=url\()[A-zА-я0-9\-\_\/\"\'\.\?\s]*(?=\))/iu', $string, $url);
            if($url[0]){
                foreach($url[0] as $u){  
                    $imgs[] = addon_url(trim($u,'\',\"'),$css_path);
                    $us[] = $u;
                }
                
                $string = str_replace($us, $imgs, $string);
            }
        }
        $fullcss .= $string;
    }
    if(isset($fullcss)){
        fwrite($f, $fullcss);
        fclose($f);
    }
}

function get_tail_addon_url($url){
    $array = explode('/',$url);
    $tail = false;
    foreach($array as $key=>$ar){
        if($tail) $tail .= '/'.$ar;
        if($array[$key-2]=='add-on'){
            $tail = $ar;
        }           
    }
    return $tail;  
}
