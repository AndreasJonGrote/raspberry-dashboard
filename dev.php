<pre>
<?php 
    $app['action'] = 'init' ;

   /**
     * HUE
     */

    function get_hue_data($category = 'groups', $id = 0) {

        $app['hue']['ip']   = '192.168.178.110' ;
        $app['hue']['id']   = '001788fffe6f7d7b' ;
        $app['hue']['user'] = 'TGeBHhCjSMPTKGCynwdFU5uMZxEVCKyxu4w5CHl9' ;

        $app['api-url']     = 'http://'.$app['hue']['ip'].'/api/'.$app['hue']['user'].'/'.$category ;
        $app['json']        = file_get_contents($app['api-url']) ;
        $app['hue']['data'] = json_decode($app['json'], true) ;

        return $app['hue']['data'] ;

    }

    if ($app['action'] == 'init' || $_GET['get'] == 'hue-control') {

        $app['hue']['lights'] = get_hue_data('lights');
        $app['hue']['groups'] = get_hue_data('groups');

        foreach($app['hue']['groups'] as $group) {

            if ($group['type'] == 'Room') {

                $i = 0 ;

                foreach($group['lights'] as $light) {
                    if ($app['hue']['lights'][$light]['type'] != 'On/Off plug-in unit') {

                        $i++ ;

                        if ($i == 1) {
                            $app['hue']['groups-output'] .= '<div><ul><li>'.$group['name'].'</li><ul>' ;
                        }

                        $app['hue']['groups-output'] .= '<li class="'.($app['hue']['lights'][$light]['state']['on'] == true?'active':'inactive').'"><a href="">'.$app['hue']['lights'][$light]['name'].'</a></li>' ;
                    }
                }

                if ($i != 0) {
                    $app['hue']['groups-output'] .= '</ul></ul></div>' ;
                }
                
            }
            
        }

    } 


    # var_dump($app['hue']['groups']);

    echo $app['hue']['groups-output'] ;

?>
