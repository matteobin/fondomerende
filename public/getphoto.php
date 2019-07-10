<?php
    //include the php dom parser    
    require '../simple_html_dom.php';
    //build the google images query

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    
    $name = (string) filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

    if (isset($name)) {
        $newname = $name;
    
        $newname = rtrim($newname, "+");
        $newname = "https://www.google.com/search?q=" . str_replace(' ', '+',$newname) .  '&tbm=isch';
        
        
        //use parser on queried page
        $html = file_get_html($newname);

        //create an array for all pics on page
        $picarray = array();
        $picurl = '';

        //find all images 
        foreach($html->find('img') as $element) {
            $picurl = $element->src;
            array_push($picarray,$picurl);
        }
        //then pick two random ones
        $picurl = $picarray[0];

        $image = file_get_contents($picurl);

        header('Content-type: image/jpeg;');
        header("Content-Length: " . strlen($image));
        echo $image;
    }
