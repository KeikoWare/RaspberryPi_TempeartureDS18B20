# RaspberryPi_TempeartureDS18B20

Inspired from https://www.element14.com/community/community/stem-academy/blog/2016/01/04/a-raspberry-pi-data-logger-for-about-25-temperature-probe-and-thingspeak
I have build my own Pi Zero temperature logger with two DS18B20 Temperature sensors

Build upon Raspbian Lite OS

First:
   $ sudo reboot
   $ sudo nano /etc/wpa-supplicant/wpa-supplicant.conf
   network={
      ssid="your-wofo-ssid"
      psk ="your-secret_key"
   }
   $ sudo reboot

Next:
   $ sudo nano /boot/config.txt
Go to the bottom of the file, and add this line:
   dtoverlay=w1-gpio
Reboot after saving
   $ sudo reboot

Third:
   $ sudo modprobe w1-gpio 
   $ sudo modprobe w1-therm

Import the Python script and edit the crontab:
   $ wget https://github.com/KeikoWare/RaspberryPi_TempeartureDS18B20/KeikoTemp.py
   $ sudo crontab -e

Add the following line at the bottom

   */10 * * * * * python /home/pi/KeikoTemp.py

   $ sudo reboot
