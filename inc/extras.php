<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package idahograce
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function idahograce_body_classes( $classes ) {
    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if ( is_front_page() || is_home() ) {
        $classes[] = 'homepage';
    } else {
        $classes[] = 'subpage';
    }

    $browsers = ['is_iphone', 'is_chrome', 'is_safari', 'is_NS4', 'is_opera', 'is_macIE', 'is_winIE', 'is_gecko', 'is_lynx', 'is_IE', 'is_edge'];
    $classes[] = join(' ', array_filter($browsers, function ($browser) {
        return $GLOBALS[$browser];
    }));

    return $classes;
}
add_filter( 'body_class', 'idahograce_body_classes' );

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}


function add_query_vars_filter( $vars ) {
  $vars[] = "pg";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );


function shortenText($string, $limit, $break=".", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}

/* Fixed Gravity Form Conflict Js */
add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
    return true;
}

function get_page_id_by_template($fileName) {
    $page_id = 0;
    if($fileName) {
        $pages = get_pages(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => $fileName.'.php'
        ));

        if($pages) {
            $row = $pages[0];
            $page_id = $row->ID;
        }
    }
    return $page_id;
}

function string_cleaner($str) {
    if($str) {
        $str = str_replace(' ', '', $str); 
        $str = preg_replace('/\s+/', '', $str);
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
        $str = strtolower($str);
        $str = trim($str);
        return $str;
    }
}

function format_phone_number($string) {
    if(empty($string)) return '';
    $append = '';
    if (strpos($string, '+') !== false) {
        $append = '+';
    }
    $string = preg_replace("/[^0-9]/", "", $string );
    $string = preg_replace('/\s+/', '', $string);
    return $append.$string;
}

function get_instagram_setup() {
    global $wpdb;
    $result = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = 'sb_instagram_settings'" );
    if($result) {
        $option = ($result->option_value) ? @unserialize($result->option_value) : false;
    } else {
        $option = '';
    }
    return $option;
}

function extract_emails_from($string){
  preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
  return $matches[0];
}

function email_obfuscator($string) {
    $output = '';
    if($string) {
        $emails_matched = ($string) ? extract_emails_from($string) : '';
        if($emails_matched) {
            foreach($emails_matched as $em) {
                $encrypted = antispambot($em,1);
                $replace = 'mailto:'.$em;
                $new_mailto = 'mailto:'.$encrypted;
                $string = str_replace($replace, $new_mailto, $string);
                $rep2 = $em.'</a>';
                $new2 = antispambot($em).'</a>';
                $string = str_replace($rep2, $new2, $string);
            }
        }
        $output = apply_filters('the_content',$string);
    }
    return $output;
}

function get_social_links() {
    $social_types = social_icons();
    $social = array();
    foreach($social_types as $k=>$icon) {
        $value = get_field($k,'option');
        if($value) {
            $social[$k] = array('link'=>$value,'icon'=>$icon);
        }
    }
    return $social;
}

function social_icons() {
    $social_types = array(
        'facebook'  => 'fab fa-facebook-f',
        'twitter'   => 'fab fa-twitter',
        'linkedin'  => 'fab fa-linkedin-in',
        'instagram' => 'fab fa-instagram',
        'youtube'   => 'fab fa-youtube',
        'snapchat'  => 'fab fa-snapchat-ghost',
    );
    return $social_types;
}

function parse_external_url( $url = '', $internal_class = 'internal-link', $external_class = 'external-link') {

    $url = trim($url);

    // Abort if parameter URL is empty
    if( empty($url) ) {
        return false;
    }

    //$home_url = parse_url( $_SERVER['HTTP_HOST'] );     
    $home_url = parse_url( home_url() );  // Works for WordPress

    $target = '_self';
    $class = $internal_class;

    if( $url!='#' ) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {

            $link_url = parse_url( $url );

            // Decide on target
            if( empty($link_url['host']) ) {
                // Is an internal link
                $target = '_self';
                $class = $internal_class;

            } elseif( $link_url['host'] == $home_url['host'] ) {
                // Is an internal link
                $target = '_self';
                $class = $internal_class;

            } else {
                // Is an external link
                $target = '_blank';
                $class = $external_class;
            }
        } 
    }

    // Return array
    $output = array(
        'class'     => $class,
        'target'    => $target,
        'url'       => $url
    );

    return $output;
}

function note_form_markup() {
    ob_start()?>
    
    <div class="sermon-note-wrap">
        <div class="noteWrap"><textarea name="note[]" class="notes"></textarea></div>
        <div class="sermon-button"><a class="sermonBtn"><i class="fas fa-edit"></i>Add notes</a></div>
    </div>

    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}


// function add_note_button_func( $atts ){
//     return "{%AddNoteButton%}";
// }
// add_shortcode( 'add_note_button', 'add_note_button_func' );



// add_action( 'init', 'extractdata' );
// function extractdata() {
//    wp_register_script( "sermonjs", get_stylesheet_directory_uri() . '/assets/js/sermon.js', array('jquery') );
//    wp_localize_script( 'sermonjs', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
//    wp_enqueue_script( 'jquery' );
//    wp_enqueue_script( 'sermonjs' );
// }



add_action( 'wp_ajax_nopriv_extract_page_content', 'extract_page_content' );
add_action( 'wp_ajax_extract_page_content', 'extract_page_content' );
function extract_page_content() {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        //$post_id =($_POST['postid']) ? $_POST['postid'] : '';
        $type =($_POST['gettype']) ? $_POST['gettype'] : '';
        $contenttype =($_POST['contenttype']) ? $_POST['contenttype'] : '';
        $content = '';
        $markup = '';
        $data = '';

        $args = array(
            'posts_per_page'=> 1,
            'post_type'     => 'sermons',
            'post_status'   => 'publish',
            'meta_query' => array(
               array(
                   'key' => 'sermon_visibility',
                   'value' => 'on',
                   'compare' => '=',
               )
            )
        );
        $post = get_posts($args);

        // // $post = get_post($post_id);
        if($post) {
            $obj = $post[0];
            $post_id = $obj->ID;
            $sermon_date = get_field("sermon_date",$post_id);
            $obj->sermon_date = $sermon_date;
            $obj->siteURL = get_site_url();
            $content = $obj->post_content;
            $inputField = '<span><input type="text" class="notes-input" name="answer[]"></span>';
            $textarea = '<div class="addNotesDiv"><a class="addtlNotesBtn"><i class="fas fa-edit"></i> <span>Add Notes</span></a><textarea class="notes-input" name="answer_multiple[]"></textarea></div>';
            $content = str_replace('[blank_field_here]',$inputField,$content);
            $content = str_replace('[additional_notes_here]',$textarea,$content);
            $obj->post_content = $content;
            $obj->datetoday = date('mdY');
            $data = $obj;
            $pageURL = get_permalink($post_id) . '?gettype=' . $type;
            if($contenttype=='form') {
                $markup = '';
            } else {
                $markup = @file_get_contents($pageURL);
            }
            
        }

        $response['html'] = $markup;
        $response['post'] = $data;
        echo json_encode($response);

    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
    die();
}


function download_sermon_notes($vars) {
    $post_id = $vars['id'];
    $notes = $vars['answer'];
    $notesTextarea = ( isset($vars['answer_multiple']) ) ? $vars['answer_multiple'] : '';
    $post = get_post($postid);
    $fileName = '';
    $siteURL = get_site_url();
    $siteName = get_bloginfo("name");
    if($post) {

        $v = get_field("scriptures",$post_id);
        $verses = ( isset($v['verses']) && $v['verses'] ) ? $v['verses'] : '';
        $content = '';
        if($verses) {
            $content .= '<p><strong><i>Text: '.$verses.'</i></strong></p><br>';
        }

        $content .= get_sermon_content($post_id);
        $title = get_the_title($post_id);
        $sermon_date = get_field("sermon_date",$post_id);
        $text = '<style>body{font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;}ul li{margin:5px 0px}ol li{margin:2px 0px}</style>';
        $text .= '<div style="text-align:center;padding: 15px 0 5px;"><img src="idaho-grace.jpg" style="width:100px;height:auto"/></div>';
        $text .= '<h1 align="center" style="font-size:20px;margin:10px 0 25px">Grace Bible Church<br>'.$siteName.'</h1><hr>';
        $text .= '<h2 style="font-size:25px;margin:25px 0 0;color:#e4812d">'.$title.'</h2>';
        $text .= '<p style="font-size:16px;margin:0 0 30px"><strong>'.$sermon_date.'</strong></p>';

        $fileName = sanitize_title($title) . '.pdf';
        if($notes) {

            $parts = explode('{%userAnswer%}',$content);

            $note_count = count($notes);
            if($parts) {
                $i=1; foreach($parts as $k=>$str) {
                    $str2 = preg_replace('/\s+/', '', $str);
                    $noteVal = '';

                    if( $str2 ) {
                        $string = ( isset($notes[$k]) && $notes[$k] ) ? $notes[$k] : '';
                        $note_txt_str = ($string) ? preg_replace('/\s+/', '', $string) : '';
                        if($i<=$note_count) {
                            if($note_txt_str) {
                                $note_txt = $notes[$k];
                            } else {
                                $note_txt = '{%NOANSWER%}';
                            }
                        } else {
                            $note_txt =  '';
                        }

                        if($note_txt) {
                            if($note_txt=='{%NOANSWER%}') {
                                $noteVal = '___________________';
                            } else {
                                $noteVal = '<strong><u>&nbsp;' . $note_txt . '&nbsp;</u></strong>';
                            }
                        }

                        $text .= $str . $noteVal;
                    }
                    
                    $i++;
                }
            }
        } else {
            $text .= str_replace('[blank_field_here]','______________________',$content);
        }

        if( isset($notesTextarea) ) {

            $textareaFields = explode('{%userAnswerMultiple%}',$text);
            $note_count = count($notesTextarea);

            $text2 = '';
            if($textareaFields) {
                $i=1; foreach($textareaFields as $k=>$str) {
                    $str2 = preg_replace('/\s+/','', $str);
                    $noteVal = '';
                    if( $str2 ) {
                        $string = ( isset($notesTextarea[$k]) && $notesTextarea[$k] ) ? $notesTextarea[$k] : '';
                        $note_txt_str = ($string) ? preg_replace('/\s+/', '', $string) : '';

                        if( $i<=$note_count ) {
                            if($note_txt_str) {
                                $note_txt = trim($notesTextarea[$k]);
                                $noteVal = '<span class="multipleInput" style="display:block;border:1px dashed #a09f9f;background: #f3f3f3;padding:15px;border-radius:5px;margin-bottom:30px">' . nl2br($note_txt) . '</span>';
                            } else {
                                //$noteVal = '<br>______________________';
                                $noteVal = '';
                            }
                        }

                        $text2 .= $str . $noteVal;
                    }
                    $i++;
                }
            } 
           
            return $text2;

        } else {
            return $text;
        }

    }

}


function email_sermon_notes($vars) {
    $post_id = $vars['id'];
    $notes = ( isset($vars['answer']) && $vars['answer'] ) ? $vars['answer'] : '';
    $user_email = ( isset($vars['user_email']) && $vars['user_email'] ) ? $vars['user_email'] : '';
    $notesTextarea = ( isset($vars['answer_multiple']) ) ? $vars['answer_multiple'] : '';

    $post = get_post($post_id);
    $fileName = '';
    $siteURL = get_site_url();
    $logo = get_bloginfo("template_url") . "/images/logo.png";
    $siteName = get_bloginfo("name");
    $content = '';
    if($post && $user_email) {
        $title = $post->post_title;

        $v = get_field("scriptures",$post_id);
        $verses = ( isset($v['verses']) && $v['verses'] ) ? $v['verses'] : '';
        $content = '';
        if($verses) {
            $content .= '<p><strong><i>Text: '.$verses.'</i></strong></p><br>';
        }
        $content .= get_sermon_content($post_id);
        $sermon_date = get_field("sermon_date",$post_id);
        $text = '<table style="border:none;border-collapse:collapse;width:100%;"><tbody><tr><td style="background-color:#FBAE6D;padding:20px;">';
        $text  .= '<table style="border:none;border-collapse: collapse;background-color:#FFFFFF;font-family:Arial,Helvetica;font-size:16px;line-height:1.3;max-width:800px;width:100%;margin:20px auto"><tbody><tr><td style="padding:20px;background:#fff;">';
        $text .= '<p style="text-align:center;margin:0 0 10px"><a href="https://www.idahograce.com/" target="_blank"><img src="https://idahogracesermon.com/idaho-grace.jpg" style="width:100px;height:auto"></a></p>';
        $text .= '<h1 align="center" style="font-size:20px;margin:10px 0 25px">Grace Bible Church<br>'.$siteName.'</h1><hr>';
        $text .= '<h2 style="font-size:25px;margin:25px 0 0;color:#e4812d">'.$title.'</h2>';
        $text .= '<p style="font-size:16px;margin:0 0 30px"><strong>'.$sermon_date.'</strong></p>';

        $fileName = sanitize_title($title) . '.pdf';
        if($notes) {

            $parts = explode('{%userAnswer%}',$content);

            $note_count = count($notes);
            if($parts) {
                $i=1; foreach($parts as $k=>$str) {
                    $str2 = preg_replace('/\s+/', '', $str);
                    $noteVal = '';

                    if( $str2 ) {
                        $string = ( isset($notes[$k]) && $notes[$k] ) ? $notes[$k] : '';
                        $note_txt_str = ($string) ? preg_replace('/\s+/', '', $string) : '';
                        if($i<=$note_count) {
                            if($note_txt_str) {
                                $note_txt = $notes[$k];
                            } else {
                                $note_txt = '{%NOANSWER%}';
                            }
                        } else {
                            $note_txt =  '';
                        }

                        if($note_txt) {
                            if($note_txt=='{%NOANSWER%}') {
                                $noteVal = '___________________';
                            } else {
                                $noteVal = '<strong style="text-decoration:none;display:inline-block;border-bottom:1px solid #000;line-height:1.2;">&nbsp;' . $note_txt . '&nbsp;</strong>';
                            }
                        }

                        $text .= $str . $noteVal;
                    }
                    
                    $i++;
                }
            }
        } else {
            $text = str_replace('[blank_field_here]','______________________',$content);
        }
        //$text  .= '</td></tr></tbody></table></td></tr></tbody></table>';

        $email_body = $text;

        if( isset($notesTextarea) ) {

            $textareaFields = explode('{%userAnswerMultiple%}',$text);
            $note_count = count($notesTextarea);

            $text2 = '';
            if($textareaFields) {
                $i=1; foreach($textareaFields as $k=>$str) {
                    $str2 = preg_replace('/\s+/','', $str);
                    $noteVal = '';
                    if( $str2 ) {
                        $string = ( isset($notesTextarea[$k]) && $notesTextarea[$k] ) ? $notesTextarea[$k] : '';
                        $note_txt_str = ($string) ? preg_replace('/\s+/', '', $string) : '';

                        if( $i<=$note_count ) {
                            if($note_txt_str) {
                                $note_txt = trim($notesTextarea[$k]);
                                $noteVal = '<span class="multipleInput" style="display:block;border:1px dashed #a09f9f;background: #f3f3f3;padding:15px;border-radius:5px;margin-bottom:30px">' . nl2br($note_txt) . '</span>';
                            } else {
                                //$noteVal = '<br>______________________';
                                $noteVal = '<br>';
                            }
                        }

                        $text2 .= $str . $noteVal;
                    }
                    $i++;
                }
            } else {
                $text2 .= "";
            }
           
            $email_body = $text2;

        } 

        $email_body .= '</td></tr></tbody></table></td></tr></tbody></table>';
        $subject = $siteName . 'Sermon Guide - ' . $title;
        $to = $user_email;
        add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
        $is_sent = wp_mail( $to,$subject,$email_body);
        return ($is_sent) ? true : false;

    }
}

function get_sermon_content($postId) {
    $getURL = get_site_url().'?plain=1&pid='.$postId;
    $pageContent = @file_get_contents($getURL);
    ob_start();
    echo $pageContent;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function convert_to_text_field($atts) {
    $a = shortcode_atts( array(
        'type' => 'single',
    ), $atts );
    $type = $a['type'];
    $output = '{%blank_field%}';
    if($type=='multiple') {
        $output = '{%blank_field_multiple%}';
    } 
    return $output;
}
add_shortcode('blank_field_here','convert_to_text_field');


function convert_additional_notes($atts) {
    // $a = shortcode_atts( array(
    //     'type' => 'single',
    // ), $atts );
    // $type = $a['type'];
    // $output = '{%blank_field%}';
    // if($type=='multiple') {
    //     $output = '{%blank_field_multiple%}';
    // } 
    return '{%additional_notes%}';
}
add_shortcode('additional_notes_here','convert_additional_notes');




