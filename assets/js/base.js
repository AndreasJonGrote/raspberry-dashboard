// Variables
var idleTimer ;
var selectedId ;

function setIdleTimer() {
    idleTimer = window.setTimeout( 
    function() {
        document.querySelector('body').classList.remove('active');
        document.querySelector('.stage .current-stage-item').classList.remove('current-stage-item') ;
        document.querySelector('.navigation .current-stage-item').classList.remove('current-stage-item') ;
        document.querySelector('.stage .calendar').classList.add('current-stage-item') ;
        document.querySelector('.navigation .calendar').classList.add('current-stage-item') ;
    }, 15000);
}

document.body.onclick = function() {
    clearTimeout( idleTimer ) ;
    setIdleTimer() ;
}

setIdleTimer() ;

// overlay
var appOverlay = document.querySelector('.overlay');
appOverlay.addEventListener("click", function() {
    document.querySelector('body').classList.add('active');
});

// menu
var navigation = document.querySelectorAll('.navigation ul li a');

Array.from(navigation).forEach(a => {
	a.addEventListener('click', function(event) {

        event.preventDefault();
        
        selectedId = event.target.dataset.id ;

        if (selectedId == 'weather-forecast') {
            updateInterface('weather-forecast');
        }

        if (selectedId == 'calendar') {
            updateInterface('calendar');
        }

        if (selectedId == 'hue-control') {
            updateInterface('hue-control');
        }

        document.querySelector('.stage .current-stage-item').classList.remove('current-stage-item') ;
        document.querySelector('.navigation .current-stage-item').classList.remove('current-stage-item') ;
        document.querySelector('.stage .'+selectedId).classList.add('current-stage-item')
        document.querySelector('.navigation .'+selectedId).classList.add('current-stage-item') ;

        return false ;

	});
});

// hue control
function hueChangeState(lid) {

    var ip      = '192.168.178.6' ;
    // var id      = '001788fffe6f7d7b' ;
    var user    = 'TGeBHhCjSMPTKGCynwdFU5uMZxEVCKyxu4w5CHl9' ;
    var url     = 'http://'+ip+'/api/'+user+'/lights/'+lid ;

    fetch(url)
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        
        var state = data.state.on ;

        const requestOptions = {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ on: !state })
        };
        fetch(url+'/state/', requestOptions) ;

        if (state == true) {
            document.querySelector('.hue-'+lid).classList.remove('active') ;
        } else {
            document.querySelector('.hue-'+lid).classList.add('active') ;
        }

    }) ;
}

const huecontrol = document.querySelectorAll('.hue-control a');

Array.from(huecontrol).forEach(a => {
	a.addEventListener('click', function(event) {

        event.preventDefault();
        selectedId = event.target.dataset.id ;
        hueChangeState(selectedId);

        return false ;

	});
});

// log details
function log(info) {

    var timestamp = new Date();
    var options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    var details = new Intl.DateTimeFormat('de-DE', options).format(timestamp);

    details = details+': '+info ;
    console.log(details);

}

/**
 * Timer Interface
 */
const customTimer = document.querySelector('.set-timer.custom') ;

const customTimerMinutes = document.querySelector('.set-timer.custom .minutes input') ;
const customTimerSeconds = document.querySelector('.set-timer.custom .seconds input') ;

const customTimerAddMinutes = document.querySelector('.set-timer.custom .minutes .add') ;
const customTimerSubMinutes = document.querySelector('.set-timer.custom .minutes .sub') ;
const customTimerAddSeconds = document.querySelector('.set-timer.custom .seconds .add') ;
const customTimerSubSeconds = document.querySelector('.set-timer.custom .seconds .sub') ;

function updateCustomTimer() {

    let minutes = parseInt(customTimerMinutes.dataset.value) ;
    let seconds = parseInt(customTimerSeconds.dataset.value) ;

    minutes = minutes * 60 ;
    seconds = minutes + seconds ;
    customTimer.setAttribute('data-value', seconds);
}

customTimerAddMinutes.addEventListener('click', function(event) {

    event.preventDefault();
    let recentValue = parseInt(customTimerMinutes.value) ;
    recentValue = recentValue + 1 ;
    customTimerMinutes.value = recentValue<10?'0'+recentValue:recentValue ;
    customTimerMinutes.setAttribute('data-value', recentValue);

    updateCustomTimer();
    return false ;

}) ;

customTimerSubMinutes.addEventListener('click', function(event) {

    event.preventDefault();
    let recentValue = parseInt(customTimerMinutes.value) ;
    recentValue = recentValue - 1 ;
    if (recentValue >= 0) {
        customTimerMinutes.value = recentValue<10?'0'+recentValue:recentValue ;
        customTimerMinutes.setAttribute('data-value', recentValue);
    }
    updateCustomTimer();
    return false ;

}) ;

customTimerAddSeconds.addEventListener('click', function(event) {

    event.preventDefault();
    let recentValue = parseInt(customTimerSeconds.value) ;
    recentValue = recentValue + 1 ;
    customTimerSeconds.value = recentValue<10?'0'+recentValue:recentValue ;
    customTimerSeconds.setAttribute('data-value', recentValue);
    updateCustomTimer();
    return false ;

}) ;

customTimerSubSeconds.addEventListener('click', function(event) {

    event.preventDefault();
    let recentValue = parseInt(customTimerSeconds.value) ;
    recentValue = recentValue - 1 ;
    if (recentValue >= 0) {
        customTimerSeconds.value = recentValue<10?'0'+recentValue:recentValue ;
        customTimerSeconds.setAttribute('data-value', recentValue);
    }
    updateCustomTimer();
    return false ;

}) ;

/**
 * Timer Script
 */

function formatTime(totalSeconds) {
    const minutes = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
    const seconds = (totalSeconds % 60).toString().padStart(2, '0');
    return `${minutes}:${seconds}`;
  }


// das HTML-Element für den Countdown-Timer und den gegebenen Timer bekommen
const timerWrapper = document.querySelector('.header .timer');
const countdownTimer = document.querySelector('.header .timer .countdown-timer');
const givenTimer = document.querySelector('.header .timer .given-timer');

let timerInterval; // speichert das Intervall für den Timer

// Funktion, um den Timer zu starten
function startTimer(time) {
  clearInterval(timerInterval); // Falls ein Timer bereits läuft, wird er gestoppt

  const startTime = Date.now(); // aktuelle Zeit wird gespeichert
  const endTime = startTime + time * 1000; // endzeit wird berechnet
  
  updateTimer(endTime - startTime); // Timer wird direkt gestartet
  
  timerInterval = setInterval(() => {
    const timeLeft = endTime - Date.now();
    if (timeLeft < 0) { // Wenn der Countdown endet
      clearInterval(timerInterval); // Timer stoppen
      fetch('ajax.php?action=buzzer-start');
      timerWrapper.classList.add('expired'); // Klasse "finished" wird hinzugefügt
    } else {
      updateTimer(timeLeft); // Timer aktualisieren
    }
  }, 1000);
}

// Funktion, um den Timer anzuzeigen
function updateTimer(time) {
  const minutes = Math.floor(time / 60000);
  const seconds = Math.floor((time % 60000) / 1000);
  countdownTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Funktion, um auf Klicks auf ".set-timer .button" zu reagieren
const setTimerButtons = document.querySelectorAll('.set-timer .button');
setTimerButtons.forEach(button => {
  button.addEventListener('click', () => {
    const time = button.parentNode.dataset.value;
    startTimer(time);
    timerWrapper.classList.add('active');
    givenTimer.textContent = 'Timer: ' + formatTime(time) ; // gegebenen Timer setzen
    if (button.parentNode.classList.contains('custom')) {
        fetch('ajax.php?action=save-timer&value=' + time);
    }
  });
});

// Funktion, um auf Klicks auf ".header .timer .button" zu reagieren
const headerButton = document.querySelector('.header .timer .button');
headerButton.addEventListener('click', () => {
  clearInterval(timerInterval);
  fetch('ajax.php?action=buzzer-end');
  timerWrapper.classList.remove('expired');
  timerWrapper.classList.remove('active');
});


/** 
 * Update Interface 
 */
function updateInterface(triggerClass) {

    fetch('ajax.php?get=' + triggerClass)
    .then(function(response) {
        return response.text();
    })
    .then(function(html) {
        document.querySelector('.sync-' + triggerClass).innerHTML = html ;

        if (triggerClass == 'hue-control') {
            var huecontrol = document.querySelectorAll('.hue-control a');
            Array.from(huecontrol).forEach(a => {
                a.addEventListener('click', function(event) {
                    event.preventDefault();
                    selectedId = event.target.dataset.id ;
                    hueChangeState(selectedId);
                    return false ;
                });
            });
        }

        log('sync ' + triggerClass); 
    }) ;

}

const clockHours   = document.querySelector('.hours');
const clockMinutes = document.querySelector('.minutes');
const clockSeconds = document.querySelector('.seconds');
const clockDate    = document.querySelector('.date');

window.setInterval(

    function () {

        let timestamp = new Date();

        clockHours.textContent = ('0' + timestamp.getHours()).substr(-2);
        clockMinutes.textContent = ('0' + timestamp.getMinutes()).substr(-2);
        clockSeconds.textContent = ':'+('0' + timestamp.getSeconds()).substr(-2) ;

        if (timestamp.getHours()+timestamp.getMinutes()+timestamp.getSeconds() == 001) {
            let options = { weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit' };
            clockDate.textContent = (new Intl.DateTimeFormat('de-DE', options).format(timestamp));
        }

        /**
         * SOME KIND OF CRONTAB
         */
        // EVERY 5 MINUTES
        if (timestamp.getSeconds() == 1 && (timestamp.getMinutes() == 0 || timestamp.getMinutes() % 5 == 0))  { 
            updateInterface('calendar') ;
            updateInterface('weather');
            updateInterface('hue');
            updateInterface('server');
        }

        // EVERY HOUR AT 59
        if (timestamp.getSeconds() == 1 && timestamp.getMinutes() == 59)  { 
            fetch('ajax.php?get=weather-datasets') ;
        }

    }, 1000
);

updateInterface('server');