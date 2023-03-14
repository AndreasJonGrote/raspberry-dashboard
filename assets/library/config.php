<?php

    /**
     * Init Configs 
     */
    date_default_timezone_set('Europe/Berlin');
    error_reporting(0) ;

    if (isset($_GET['date']) && $_GET['date'] != '') {
        $app['initinal-date'] = strtotime($_GET['date']) ; 
    } else {
        $app['initinal-date'] = time() ;
    }

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    /**
     * Translate Defaults
     */
    $weekdays['short'] = array(
        'Mon' => 'Mo',
        'Tue' => 'Di',
        'Wed' => 'Mi',
        'Thu' => 'Do',
        'Fri' => 'Fr',
        'Sat' => 'Sa',
        'Sun' => 'So'
    ) ;

    $weekdays['long'] = array(
        'Mon' => 'Montag',
        'Tue' => 'Dienstag',
        'Wed' => 'Mittwoch',
        'Thu' => 'Donnerstag',
        'Fri' => 'Freitag',
        'Sat' => 'Samstag',
        'Sun' => 'Sonntag'
    ) ;

    /**
     * Weather Icon Translate
     */
    $weathericons = array(
        '01d@2x.png' => 'clear-day.svg',
        '01n@2x.png' => 'clear-night.svg',
        '02d@2x.png' => 'partly-cloudy-day-drizzle.svg',
        '02n@2x.png' => 'partly-cloudy-night-drizzle.svg',
        '03d@2x.png' => 'cloudy.svg',
        '03n@2x.png' => 'cloudy.svg',
        '04d@2x.png' => 'extreme.svg',
        '04n@2x.png' => 'extreme.svg',
        '09d@2x.png' => 'extreme-rain.svg',
        '09n@2x.png' => 'extreme-rain.svg',
        '10d@2x.png' => 'extreme-day-drizzle.svg',
        '10n@2x.png' => 'extreme-night-drizzle.svg',
        '11d@2x.png' => 'thunderstorms-day-overcast-rain.svg',
        '11n@2x.png' => 'thunderstorms-night-overcast-rain.svg',
        '13d@2x.png' => 'snow.svg',
        '13n@2x.png' => 'snow.svg',
        '50d@2x.png' => 'partly-cloudy-day-haze.svg',
        '50n@2x.png' => 'partly-cloudy-night-haze.svg'
    ) ;

    /**
     * Read JSON Database
     */

    /** Timer */
    function secondsToIs($seconds) {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return ['minutes' => ($minutes<10?'0'.$minutes:$minutes), 'seconds' => ($seconds<10?'0'.$seconds:$seconds)] ;
      }

    $app['database']['timer'] = json_decode(file_get_contents('assets/database/timer.json'), true) ;
    $app['database']['timer']['output'] = secondsToIs($app['database']['timer']['value']) ;
    

    /**
     * SUDO & HEADER Commands
     */
    if (isset($_GET['action'])) {

        if ($_GET['action'] == 'reboot') {
            shell_exec('sudo shutdown -r +1');
            header('location: ?action=reboot-inc');
            exit();
        }

        if ($_GET['action'] == 'reboot-now') {
            shell_exec('sudo reboot');
            header('location: ?action=reboot-instant');
            exit();
        }

        if ($_GET['action'] == 'reboot-update-system') {
            shell_exec('sudo apt-get update -y && sudo apt-get upgrade -y && sudo reboot');
            header('location: ?action=shutdown-instant');
            exit();
        }
        
        if ($_GET['action'] == 'shutdown') {
            shell_exec('sudo shutdown -h +1');
            header('location: ?action=shutdown-inc');
            exit();
        }

        if ($_GET['action'] == 'shutdown-now') {
            shell_exec('sudo shutdown now');
            header('location: ?action=shutdown-instant');
            exit();
        }

        if ($_GET['action'] == 'shutdown-update-system') {
            shell_exec('sudo apt-get update -y && sudo apt-get upgrade -y && sudo shutdown');
            header('location: ?action=shutdown-instant');
            exit();
        }

        if ($_GET['action'] == 'buzzer-test') {
            shell_exec('sudo python assets/python/buzzer-test.py');
        }

        if ($_GET['action'] == 'buzzer-start') {
            shell_exec('sudo python assets/python/buzzer.py');
        }

        if ($_GET['action'] == 'buzzer-end') {
            shell_exec("sudo pkill -f buzzer.py");
        }

        if ($_GET['action'] == 'save-timer') {
            $json['value'] = $_GET['value'] ;
            file_put_contents('assets/database/timer.json', json_encode($json)) ;
        }

    }
    
    if (isset($app['action']) || isset($_GET['get'])) {

        /**
         * Server
         */
         if (isset($_GET['get']) && $_GET['get'] == 'server') {
            
            $server['output'] = '' ;
            
            /** BackUp-Server */
            $ping = exec('ping -c 1 192.168.178.4', $output, $status) ;
            
            $server['output'] .= '<span>' ;
            if ($status == 0) {
                $server['output'] .= '<img src="assets/icons/server-green.svg" alt="" class="backupserver">' ;
            } else {
                $server['output'] .= '<img src="assets/icons/server-white.svg" alt="" class="backupserver">' ;
            }
            $server['output'] .= '<em>B</em></span>' ;
            
            /** Daten-Server */
            $ping = exec('ping -c 1 192.168.178.5', $output, $status) ;

            $server['output'] .= '<span>' ;
            if ($status == 0) {
                $server['output'] .= '<img src="assets/icons/server-green.svg" alt="" class="fileserver">' ;
            } else {
                $server['output'] .= '<img src="assets/icons/server-white.svg" alt="" class="fileserver">' ;
            }
            $server['output'] .= '<em>D</em></span>' ;

            /** Sync */
            if (isset($_GET['get']) && $_GET['get'] == 'server') {
                echo $server['output'] ;
            }

        }

        /**
         * WEATHER
         */
        $app['weather']['api-key']              = '64a0e01a5c1d64d534b5ec5a10471a89' ;
        $app['weather']['city-id']              = '2949188' ;

        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'weather')) {

            $app['weather']['recent']['api-call']   = 'https://api.openweathermap.org/data/2.5/weather?id='.$app['weather']['city-id'].'&appid='.$app['weather']['api-key'].'&lang=de&units=metric' ;
            $app['weather']['recent']['json']       = file_get_contents($app['weather']['recent']['api-call']) ;
            $app['weather']['recent']['data']       = json_decode($app['weather']['recent']['json'], true) ;

            $app['weather']['recent']['sunrise']    = $app['weather']['recent']['data']['sys']['sunrise'] ;
            $app['weather']['recent']['sunset']     = $app['weather']['recent']['data']['sys']['sunset'] ;

            $weather = '<div class="temp"><img src="assets/icons/animated/'.$weathericons[$app['weather']['recent']['data']['weather'][0]['icon'].'@2x.png'].'">'.str_replace('.', ',', round($app['weather']['recent']['data']['main']['temp'])).'°</div>' ;

            if (isset($_GET['get']) && $_GET['get'] == 'weather') {
                echo $weather ;
            }

        }

        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'weather-forecast')) {

            $app['weather']['forecast']['api-call'] = 'https://api.openweathermap.org/data/2.5/forecast?id='.$app['weather']['city-id'].'&appid='.$app['weather']['api-key'].'&lang=de&units=metric' ;
            $app['weather']['forecast']['json']     = file_get_contents($app['weather']['forecast']['api-call']) ;
            $app['weather']['forecast']['data']     = json_decode($app['weather']['forecast']['json'], true) ;
            
            $weatherForecast = '<div class="wrapper">' ;
            $i = 0 ; foreach($app['weather']['forecast']['data']['list'] as $key => $value) { 
               
                if ($i < 5 && (date('H', $value['dt']) == '11' || date('H', $value['dt']) == '12' || date('H', $value['dt']) == '13')) {
                    #var_dump($value);
                    $i++ ;
                    $weatherForecast .= '<div class="upcoming">' ;
                    $weatherForecast .= '<span class="day">'.$weekdays['short'][date('D', $value['dt'])].'. '.date('d.n.Y', $value['dt']).'</span>' ;
                    $weatherForecast .= '<span class="icon"><img src="assets/icons/animated/'.$weathericons[$value['weather'][0]['icon'].'@2x.png'].'" alt="" /></span>' ;
                    $weatherForecast .= '<span class="temp">'.str_replace('.', ',', round($value['main']['temp'])).'°</span>' ;
                    $weatherForecast .= '</div>' ;
                }
            }
            $weatherForecast .= '</div>' ;

           # exit();

            if (isset($_GET['get']) && $_GET['get'] == 'weather-forecast') {
                echo $weatherForecast ;
            }

        }

        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'weather-datasets')) {

            $app['weather']['recent']['api-call']   = 'https://api.openweathermap.org/data/2.5/weather?id='.$app['weather']['city-id'].'&appid='.$app['weather']['api-key'].'&units=metric' ;
            $app['weather']['recent']['json']       = file_get_contents($app['weather']['recent']['api-call']) ;
            $app['weather']['recent']['data']       = json_decode($app['weather']['recent']['json'], true) ;

            unset($app['weather']['recent']['data']['weather'][0]['id']) ;
            $temp = 'temp/weather/'.str_replace(' ', '_', implode(' - ', $app['weather']['recent']['data']['weather'][0])).'.txt' ;
            if (!file_exists($temp)) {
                touch($temp);
                file_put_contents($temp, json_encode($app['weather']));
            }

            $app['weather']['forecast']['api-call'] = 'https://api.openweathermap.org/data/2.5/forecast?id='.$app['weather']['city-id'].'&appid='.$app['weather']['api-key'].'&units=metric' ;
            $app['weather']['forecast']['json']     = file_get_contents($app['weather']['forecast']['api-call']) ;
            $app['weather']['forecast']['data']     = json_decode($app['weather']['forecast']['json'], true) ;
            
            foreach($app['weather']['forecast']['data']['list'] as $key => $value) {
                unset($value['weather'][0]['id']) ;
                $temp = 'temp/weather/'.str_replace(' ', '_', implode(' - ', $value['weather'][0])).'.txt' ;
                if (!file_exists($temp)) {
                    touch($temp);
                    file_put_contents($temp, json_encode($value));
                }
            }

        }

        /**
         * HUE
         */
        function get_hue_data($category = 'lights', $id = 0) {

            $app['hue']['ip']   = '192.168.178.6' ;
            $app['hue']['id']   = '001788fffe6f7d7b' ;
            $app['hue']['user'] = 'TGeBHhCjSMPTKGCynwdFU5uMZxEVCKyxu4w5CHl9' ;

            $app['api-url']     = 'http://'.$app['hue']['ip'].'/api/'.$app['hue']['user'].'/'.$category ;
            $app['json']        = file_get_contents($app['api-url']) ;
            $app['hue']['data'] = json_decode($app['json'], true) ;

            return $app['hue']['data'] ;

        }

        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'hue')) {

            $app['hue']['data'] = get_hue_data();

            /**
             * DEVICE TYPES
             * 
             * LAMPS
             * Extended color light
             * Dimmable light
             * Color temperature light
             * 
             * STECKDOSEN
             * On/Off plug-in unit
             */

            foreach($app['hue']['data'] as $device) {
                if ($device['state']['on'] == true) {
                    if ($device['type'] != 'On/Off plug-in unit') {
                        $app['hue']['devices'][] = $device['name'] ;
                    }
                }
            }

            $app['hue']['devices'] = count($app['hue']['devices']) ;

            if (isset($_GET['get']) && $_GET['get'] == 'hue') {
                echo '<img src="assets/icons/streamline-icon-table-lamp@32x32.svg" alt="" /><span>'.$app['hue']['devices'].'</span>' ;
            }

        } 

        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'hue-control')) {

            $app['hue']['lights'] = get_hue_data('lights');
            $app['hue']['groups'] = get_hue_data('groups');
            $app['hue']['control'] = null ;

            foreach($app['hue']['groups'] as $group) {

                if ($group['type'] == 'Room') {

                    $i = 0 ;

                    foreach($group['lights'] as $light) {
                        if ($app['hue']['lights'][$light]['type'] != 'On/Off plug-in unit') {

                            $i++ ;

                            $app['hue']['control'] .= '<a href="#" data-id="'.$light.'" class="hue-'.$light.' '.($app['hue']['lights'][$light]['state']['on'] == true?'active':'inactive').'">
                            <span>'.$group['name'].'</span>
                            '.$app['hue']['lights'][$light]['name'].'
                            </a>' ;
                            
                        }
                    }

                    if ($i != 0) {
                        $app['hue']['control'] .= '<div class="group-end"></div>' ;
                    }
                    
                }
                
            }

            if (isset($_GET['get']) && $_GET['get'] == 'hue-control') {
                echo $app['hue']['control'] ;
            }

        } 

        /**
         * CALENDAR
         */
        if ((isset($app['action']) || isset($_GET['get'])) && ($app['action'] == 'init' || $_GET['get'] == 'calendar')) {

            $app['calendar']['first-day-of-week'] = date('Y-m-d', strtotime('monday this week', $app['initinal-date'])) ;
            $app['calendar']['initinal-date'] = date('Y-m-d', $app['initinal-date']) ;

            /**
             * RETRIEVE ALL CALENDERS ICS
             */
            include('assets/library/iCalEasyReader.php') ;

            $ical = new iCalEasyReader();
            $lines = $ical->load(file_get_contents('assets/library/feiertage_2022.ics'));
            
            foreach($lines['VEVENT'] as $dates) {
                if ($dates["DTSTART"]['TYPE'] == 'DATE') {
                    $timestamp = iCalDateToUnixTimestamp($dates["DTSTART"]['VALUE']) ;
                if (date('Y', strtotime($dates["DTSTART"]['VALUE'])) == date('Y') || date('Y', strtotime($dates["DTSTART"]['VALUE'])) == date('Y')+1) {
                        $holidays[date('Y-m-d', $timestamp)] = $dates['SUMMARY'] ;
                    }
                }
            }
        
            $lines = $ical->load(file_get_contents('https://p55-caldav.icloud.com/published/2/ODk5OTg5Mzk4OTk5ODkzOR2y1cZTASZLsudAaSPNIqLWaoe-V0RPgg8hFVpZDsbS51muqtutuxuBno-Sx66Ob0jf50pqWgtfPeXLDV0Y3ww'));
            #$lines = $ical->load(file_get_contents('webcal://p102-caldav.icloud.com/published/2/MTA3MjMwMjgyMTEwNzIzMFs_8jM8LN9Q9oSLwDKOGFiLbw_WA4CBb9-Bvg42gKOhxafSDKPk_cq_oo41hj-pukU7pHGbqWw1Lgn2ljdLw5M'));

            foreach($lines['VEVENT'] as $dates) {
                $timestamp = iCalDateToUnixTimestamp($dates["DTSTART"]['VALUE']) ;
                if (date('Y', strtotime($dates["DTSTART"]['VALUE'])) == date('Y') || date('Y', strtotime($dates["DTSTART"]['VALUE'])) == date('Y')+1) {
                    $events[date('Y-m-d', $timestamp)][date('H:i', $timestamp)][] = $dates['SUMMARY'] ;
                }
            }

            $daycount = 0 ;
            $calendar = '' ;

            $calendar .= '<div class="week titles">' ;
            foreach($weekdays['short'] as $day) {
                $calendar .= '<div class="day '.strtolower($day).'"><div class="date">'.$day.'</div></div>' ;
            }
            $calendar .= '</div>' ;

            $calendar .= '<div class="week">' ;
            for($i = 1; $i <= 14; $i++) {

                $recentdate = date('Y-m-d', strtotime('+'.$daycount.'days '.$app['calendar']['first-day-of-week'])) ;

                if ($daycount == 7) {
                    $calendar .= '</div><div class="week">' ;
                }

                if ($daycount == 0 || $daycount == 7) {
                    $calendar .= '<div class="weeknumber">'.date('W', strtotime($recentdate)).'</div>' ;
                }
                
                $calendar .= '<div class="day '.strtolower(date('D', strtotime($recentdate))).' '.(isset($holidays) && $holidays[date('Y-m-d', strtotime($recentdate))] != '' ? 'holiday' : '').' '.($recentdate==$app['calendar']['initinal-date']? 'today' : '').'">
                                <div class="date">'.date('d.m.', strtotime($recentdate)).'</div>
                                <div class="events">' ;

                if (isset($holidays) && $holidays[date('Y-m-d', strtotime($recentdate))] != '') {
                    $calendar .= '<div class="event holiday">'.$holidays[date('Y-m-d', strtotime($recentdate))].'</div>' ;
                }

                if (isset($events) && is_array($events[date('Y-m-d', strtotime($recentdate))])) {
                    ksort($events[date('Y-m-d', strtotime($recentdate))]);
                    foreach($events[date('Y-m-d', strtotime($recentdate))] as $time => $event) {
                        foreach($event as $title) {
                            $calendar .= '<div class="event"><span class="title">'.$title.'</span><span class="timeslot">'.$time.'</span></div>' ;
                        }
                        
                    }
                }

                $calendar .= '</div></div>' ;

                $daycount++ ; 

            }

            $calendar .= '</div>' ;

            if (isset($_GET['get']) && $_GET['get'] == 'calendar') {
                echo $calendar ;
            }
        
        }
    
    }

    /**
     * NUKI
     */
    function get_nuki_data($action = 'state') {

        $app['nuki']['ip']           = '192.168.178.7:8080' ;
        $app['nuki']['deviceType']   = '4' ;
        $app['nuki']['deviceID']     = '819456100' ;
        $app['nuki']['token']        = 'jonessa' ;

        if ($action == 'unlock') {
            $app['api-url'] = 'http://'.$app['nuki']['ip'].'/unlock?nukiId='.$app['nuki']['deviceID'].'&deviceType='.$app['nuki']['deviceType'].'&token='.$app['nuki']['token'] ;
        } else if ($action == 'lock') {
            $app['api-url'] = 'http://'.$app['nuki']['ip'].'/lock?nukiId='.$app['nuki']['deviceID'].'&deviceType='.$app['nuki']['deviceType'].'&token='.$app['nuki']['token'] ;
        } else {
            $app['api-url'] = 'http://'.$app['nuki']['ip'].'/lockState?nukiId='.$app['nuki']['deviceID'].'&deviceType='.$app['nuki']['deviceType'].'&token='.$app['nuki']['token'] ;
        }

        echo $app['json']        = file_get_contents($app['api-url']) ;
        #$app['nuki']['data'] = json_decode($app['json'], true) ;

        return $app['nuki']['data'] ;

    }

    // $nuki = get_nuki_data(); 
    // var_dump($nuki);
    // exit();


?>