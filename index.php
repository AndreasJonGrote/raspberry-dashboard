<?php 
    
    $app['action'] = 'init' ;
    include 'assets/library/config.php' ; 

?>

<!DOCTYPE html>
<html lang="de-DE">

<head>

	<meta charset="UTF-8" />

	<title>Jonessa</title>

    <!-- LOAD Basic CSS Files -->
    <link href="assets/css/typography.min.css" rel="stylesheet" />
    <link href="assets/css/base.min.css?<?php echo time() ; ?>" rel="stylesheet" />

</head>

    <body class="active">

        <div class="overlay">
            
        </div>

        <div class="frame">

            <div class="navigation">
            
                <ul>
                    <li><a href="#" data-id="calendar" class="calendar current-stage-item"><img src="assets/icons/streamline-icon-calendar@32x32.svg" alt="" /></a></li>
                    <li><a href="#" data-id="timer" class="timer"><img src="assets/icons/feathericons-clock@32x32.svg" alt="" /></a></li>
                    <li><a href="#" data-id="weather-forecast" class="weather-forecast"><img src="assets/icons/streamline-icon-weather-cloud@32x32.svg" alt="" /></a></li>
                    <li><a href="#" data-id="hue-control" class="hue-control"><img src="assets/icons/lamp.svg" alt="" /></a></li>
                    <li><a href="#" data-id="system" class="system"><img src="assets/icons/streamline-icon-cog@32x32.svg" alt="" /></a></li>
                </ul>

            </div>
            
            <div class="header">

                <div class="clock sync-clock">
                    <div class="date"><?php echo $weekdays['long'][date('D', $app['initinal-date'])].', '.date('d.m.Y', $app['initinal-date']) ; ?></div>
                    <div class="time">
                        <span>
                            <span class="hours"><?php echo date('H') ; ?></span>:<span class="minutes"><?php echo date('i') ; ?></span><span class="seconds">:<?php echo date('s') ; ?></span>
                        </span>
                    </div>
                </div>

                <div class="timer sync-timer">
                    <a href="#" class="button">
                        <div class="given-timer">00:01:30</div>
                        <div class="countdown-timer">00:00:47</div>
                    </a>
                </div>
                
                <div class="weather sync-weather">
                    <?php echo $weather ; ?>
                </div>

                <div class="widgets">

                    <div class="server sync-server">
                        <span>
                            <img src="assets/icons/server-white.svg" alt="" class="backupserver" />
                            <em>B</em>
                        </span>
                        <span>
                            <img src="assets/icons/server-white.svg" alt="" class="fileserver" />
                            <em>D</em>
                        </span>
                    </div>

                    <div class="hue sync-hue">
                        <img src="assets/icons/streamline-icon-table-lamp@32x32.svg" alt="" />
                        <span><?php echo $app['hue']['devices'] ; ?></span>
                    </div>

                    <div class="sunset">
                        <img src="assets/icons/animated/sunset-light.svg" alt="" />
                        <span><?php echo date('H:i', $app['weather']['recent']['sunset']) ; ?>h</span>
                    </div>

                </div>

            </div>

            <div class="stage">

                <div class="system">
                    <div class="wrapper">

                        <a href="?action=refresh">
                            <span>App</span>
                            Refresh
                            
                        </a>

                        <a href="?action=reboot-update-system">
                            <span>System</span>
                            Update
                            
                        </a>

                        <a href="?action=reboot-now">
                            <span>System</span>
                            Reboot
                        </a>
                        
                        <a href="?action=shutdown-now" class="important">
                            <span>System</span>
                            Shutdown
                        </a>

                        <a href="?action=buzzer-test">
                            <span>Buzzer</span>
                            Test
                        </a>

                        <a href="?action=buzzer-end" class="important">
                            <span>Buzzer</span>
                            Stop 
                        </a>

                    </div>

                    <div class="infos">
                        Device-IP: <?php echo getHostByName(getHostName()) ; ?><br />
                        Last Refresh: <?php echo date('H:i:s d.m.Y') ; ?><br />
                        Last Deploy: <?php echo date ('H:i:s d.m.Y', filemtime('index.php')); ?>
                    </div>

                </div>

                <div class="timer">

                    <div class="wrapper">
                        <div class="set-timer" data-value="300">
                            <div class="wrapper"><span>05:00</span></div>
                            <a href="#" class="button">Starten</a>
                        </div>
                        <div class="set-timer" data-value="600">
                            <div class="wrapper"><span>10:00</span></div>
                            <a href="#" class="button">Starten</a>
                        </div>
                        <div class="set-timer" data-value="900">
                            <div class="wrapper"><span>15:00</span></div>
                            <a href="#" class="button">Starten</a>
                        </div>
                        <div class="set-timer" data-value="1800">
                            <div class="wrapper"><span>30:00</span></div>
                            <a href="#" class="button">Starten</a>
                        </div>
                        <div class="set-timer custom" data-value="<?php echo $app['database']['timer']['value'] ; ?>">
                            <div class="wrapper">
                                <div class="minutes">
                                    <input type="text" value="<?php echo $app['database']['timer']['output']['minutes'] ; ?>" data-value="<?php echo $app['database']['timer']['output']['minutes'] ; ?>">
                                    <a href="#" class="add">
                                        <img src="assets/icons/plus.svg" />
                                    </a>
                                    <a href="#" class="sub">
                                        <img src="assets/icons/minus.svg" />
                                    </a>
                                </div>
                                <div class="seperator">:</div>
                                <div class="seconds">
                                    <input type="text" value="<?php echo $app['database']['timer']['output']['seconds'] ; ?>" data-value="<?php echo $app['database']['timer']['output']['seconds'] ; ?>">
                                    <a href="#" class="add">
                                        <img src="assets/icons/plus.svg" />
                                    </a>
                                    <a href="#" class="sub">
                                        <img src="assets/icons/minus.svg" />
                                    </a>
                                </div>
                            </div>
                            <a href="#" class="button">Starten</a>
                        </div>
                    </div>

                </div>

                <div class="calendar sync-calendar  current-stage-item">
                    <?php echo $calendar ; ?>
                </div>

                <div class="weather-forecast sync-weather-forecast">
                    <?php echo $weatherForecast ; ?>
                </div>

                <div class="hue-control sync-hue-control">
                    <?php echo $app['hue']['control'] ; ?>
                </div>


            </div>
            
        </div>
        
        <script defer src="assets/js/base.js?<?php echo time() ; ?>"></script>

	</body>

</html>