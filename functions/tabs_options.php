<?php

add_filter('admin_options_wprecall','get_tablist_options');
function get_tablist_options($content){
    global $tabs_rcl,$rcl_options;
    
    if(!$tabs_rcl) {
        $content .= '<h3>Ни одной вкладки личного кабинета не найдено</h3>';
        return $content;
    }
    
    add_sortable_scripts();

    $opt = new Rcl_Options('tabs');

    $tabs = '<p>Сортируйте вкладки перетаскивая их на нужную позицию</p>'
            . '<ul id="tabs-list-rcl" class="sortable">';
    
    if(isset($rcl_options['tabs']['order'])){
        foreach($rcl_options['tabs']['order'] as $order=>$key){
            if(!isset($tabs_rcl[$key])) continue;
            $tabs .= get_tab_option($key);
            $keys[$key] = 1;
        }
        foreach($tabs_rcl as $key=>$tab){
            if(isset($keys[$key])) continue;
            $tabs .= get_tab_option($key,$tab);
        }
    }else{     

        foreach($tabs_rcl as $key=>$tab){
            $order = $tab['args']['order'];
            if (isset($order)) {
                if (!isset($otabs[$order])) {                    
                    $otabs[$order][$key] = $tab;
                }else {
                    for($a=$order;1==1;$a++){
                        if(!isset($otabs[$a])){
                            $otabs[$a][$key] = $tab;
                            break;
                        }
                    }
                }
            } 
        }
        
        foreach($tabs_rcl as $key=>$tab){
            if (!isset($tab['args']['order'])) {
                $otabs[][$key] = $tab;
            } 
        }
        
        ksort($otabs);

        foreach($otabs as $order=>$vals){  
            foreach($vals as $key=>$val){
                $tabs .= get_tab_option($key,$val);               
            }
        }
    }
    $tabs .= '</ul>';
    
    $tabs .= '<script>jQuery(function(){jQuery(".sortable").sortable();return false;});</script>';
        
    $content .= $opt->options('Настройка вкладок',$opt->option_block(array($tabs)));
	
    return $content;
}

function get_tab_option($key,$tab=false){
    global $rcl_options;
    $name = (isset($rcl_options['tabs']['name'][$key])) ?$rcl_options['tabs']['name'][$key] :  $tab['name'];
    return '<li>'
            . 'Имя вкладки: <input type="text" name="tabs[name]['.$key.']" value="'.$name.'">'
            . '<input type="hidden" name="tabs[order][]" value="'.$key.'">'
            . '</li>';
}

add_filter('tab_data_rcl','edit_options_tab_rcl');
function edit_options_tab_rcl($tab){
    global $rcl_options;
    if(isset($rcl_options['tabs']['name'][$tab['id']])) $tab['name'] = $rcl_options['tabs']['name'][$tab['id']];

    if(isset($rcl_options['tabs']['order'])){
        foreach($rcl_options['tabs']['order'] as $order=>$key){
            if($key!=$tab['id']) continue;
                $tab['args']['order'] = $order+10;
        }
    }
    
    return $tab;
}