import time
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BOARD)
PIEZO_PIN = 37

print("Buzzer triggert")

GPIO.setup(PIEZO_PIN, GPIO.OUT)

GPIO.output(PIEZO_PIN, True)
time.sleep(0.15)
GPIO.output(PIEZO_PIN, False)
time.sleep(0.05)

GPIO.output(PIEZO_PIN, True)
time.sleep(0.15)
GPIO.output(PIEZO_PIN, False)
time.sleep(0.05)

GPIO.output(PIEZO_PIN, True)
time.sleep(0.15)
GPIO.output(PIEZO_PIN, False)
time.sleep(0.05)

GPIO.cleanup()
