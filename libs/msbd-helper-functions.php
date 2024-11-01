<?php

if ( !function_exists('msbd_extract_array') ) {
    
    function msbd_extract_array($data, $key) {
        $keys = explode(".", $key);

        if(count($keys)==1 && isset($data[$key])) {
            return $data[$key];
        } else if(count($keys)==2) {
            if( isset($data[$keys[0]]) && isset($data[$keys[0]][$keys[1]]) ) {
                return $data[$keys[0]][$keys[1]];
            }
        }
        
        return '';
    }
}




if ( !function_exists('msbd_sanitization') ) {
    /*
     * @ $field_type = text, email, number, html, no_html, custom_html, html_js default text
     */
    function msbd_sanitization($data, $field_type='text', $oArray=array()) {        
        
        $output = '';

        switch($field_type) {
            
            /*case 'array-text':
                $output = is_array($data) ? $data : array();
                break;*/
            
            case 'number':
                $output = sanitize_text_field($data);
                $output = intval($output);
                break;
            
            case 'boolean':
                $var_permitted_values = array('y', 'n', 'true', 'false', '1', '0', 'yes', 'no');
                $output = in_array($data, $var_permitted_values) ? $data : 0;//returned false if not valid
                break;
            
            case 'email':
                $output = sanitize_email($data);
                $output = is_email($output);//returned false if not valid
                break;
                
            case 'textarea': 
                $output = esc_textarea($data);
                break;
            
            case 'html':                                         
                $output = wp_kses_post($data);
                break;
            
            case 'custom_html':                    
                $allowedTags = isset($oArray['allowedTags']) ? $oArray['allowedTags'] : "";                                        
                $output = wp_kses($data, $allowedTags);
                break;
            
            case 'no_html':                                        
                $output = strip_tags( $data );
                break;
            
            
            case 'html_js':
                $output = $data;
                break;
            
            
            case 'text':
            default:
                
                if(is_array($data)) {
                    $output = array();
                    foreach($data as $i=>$v) {
                        $output[$i] = msbd_sanitization($v, $field_type);
                    }
                } else {
                    $output = sanitize_text_field($data);
                }

                break;
        }
        
        return $output;
    }
}




if ( !function_exists('msbd_current_url') ) {
    /**
     * get URL function
     **/
    function msbd_current_url($atts) {
        // if multisite has been set to true
        if (isset($atts['multisite'])) {
            global $wp;
            $url = add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
            return esc_url($url);
        }

        // add http
        $urlCurrentPage = 'http';

        // add s to http if required
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $urlCurrentPage .= "s";
        }

        // add colon and forward slashes
        $urlCurrentPage .= "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        // return url
        return esc_url($urlCurrentPage);
    }
}



if ( !function_exists('msbd_fspace') ) {
    /**
     * Add an space before string
     *
     * @param string   $str
     *
     * @return string
     */
    function msbd_fspace( $str ) {
        return (trim($str)!='') ? ' '.trim($str) : '';
    }
}



if ( !function_exists('mps_ecssc') ) {
    /**
     * Extra CSS class helper
     *
     * @param array   $atts Shortcode attributes
     *
     * @return string
     */
    function mps_ecssc( $atts ) {
        return ( $atts['class'] ) ? ' ' . trim( $atts['class'] ) : '';
    }
}


if ( !function_exists('draw_position_select_box') ) {
    function draw_position_select_box($att, $selVal='') {
        
        $record = array(
            "after"   => "After",
            "before"     => "Before",
            "both"     => "After and Before"
        );
        
        $html = '<select '.$att.'>';
        foreach($record as $i=>$v) {
            if($selVal==$i)
                $html .= '<option value="'.$i.'" selected="selected">'.$v.'</option>';
            else
                $html .= '<option value="'.$i.'">'.$v.'</option>';
        }
        $html .= '</select>';
        
        return $html;
    }
}   


if ( !function_exists('draw_yes_no_select_box') ) {
    function draw_yes_no_select_box($att, $selVal='') {
        
        $record = array(
            "yes"   => "Yes",
            "no"     => "No"
        );
        
        $html = '<select '.$att.'>';
        foreach($record as $i=>$v) {
            if($selVal==$i)
                $html .= '<option value="'.$i.'" selected="selected">'.$v.'</option>';
            else
                $html .= '<option value="'.$i.'">'.$v.'</option>';
        }
        $html .= '</select>';
        
        return $html;
    }
}


if ( !function_exists('draw_post_types_select_box') ) {
    function draw_post_types_select_box($att, $selVal='') {
        
        $record = get_post_types();
        
        $html = '<select '.$att.'>';
        foreach($record as $i=>$v) {            
            $isSelected = (is_array($selVal) && in_array($i, $selVal)) ? true : false;            
            
            if($isSelected)
                $html .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
            else
                $html .= '<option value="'.$i.'">'.$i.'</option>';
        }
        $html .= '</select>';
        
        return $html;
    }
}


if ( !function_exists('msbd_redirect') ) {
    function msbd_redirect($url, $statusCode = '') {
        if(empty($statusCode))
            header('Location: ' . $url, true);
        else
            header('Location: ' . $url, true, $statusCode);
            
        exit();
    }
}
