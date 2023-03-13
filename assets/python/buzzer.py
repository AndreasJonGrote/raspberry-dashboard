import time
import subprocess
import RPi.GPIO as GPIO

PIEZO_PIN = 37

GPIO.setmode(GPIO.BOARD)
GPIO.setup(PIEZO_PIN, GPIO.OUT)

subprocess.run(["sudo", "vcgencmd", "display_power", "1"])

print("Buzzer triggert")

for i in range(50):

    GPIO.output(PIEZO_PIN, True)
    time.sleep(0.05)
    GPIO.output(PIEZO_PIN, False)
    time.sleep(0.05)

    GPIO.output(PIEZO_PIN, True)
    time.sleep(0.05)
    GPIO.output(PIEZO_PIN, False)
    time.sleep(0.05)

    GPIO.output(PIEZO_PIN, True)
    time.sleep(0.05)
    GPIO.output(PIEZO_PIN, False)
    time.sleep(1)

GPIO.cleanup()