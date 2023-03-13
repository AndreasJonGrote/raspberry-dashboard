import RPi.GPIO as GPIO
import time
import subprocess

# Pin-Nummer
PIR_PIN = 36

# Timeout in Sekunden
TIME_OUT = 600

# Status des Displays (0 = aus, 1 = an)
DISPLAY_STATUS = 1

# GPIO-Setup
GPIO.setmode(GPIO.BOARD)
GPIO.setup(PIR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

# Startzeitpunkt des Timers
start_time = time.time()

# Display einschalten
subprocess.run(["sudo", "vcgencmd", "display_power", "1"])

# Funktion, die ausgeführt wird, wenn Bewegung erkannt wird
def on_motion_detected(channel):
    global start_time, DISPLAY_STATUS
    
    # Timer neustarten
    start_time = time.time()
    
    # Wenn Display ausgeschaltet ist, einschalten und Status aktualisieren
    if DISPLAY_STATUS == 0:
        subprocess.run(["sudo", "vcgencmd", "display_power", "1"])
        DISPLAY_STATUS = 1

# Event-Handler registrieren
GPIO.add_event_detect(PIR_PIN, GPIO.RISING, callback=on_motion_detected)

# Hauptprogramm
while True:
    # Wenn der Timeout durchgelaufen ist, Display ausschalten und Status aktualisieren
    if time.time() - start_time > TIME_OUT and DISPLAY_STATUS == 1:
        subprocess.run(["sudo", "vcgencmd", "display_power", "0"])
        DISPLAY_STATUS = 0
    
    time.sleep(10) # Kurze Pause, um CPU-Auslastung zu reduzieren
