# RaspberryPi_TempeartureDS18B20

Inspired from https://www.element14.com/community/community/stem-academy/blog/2016/01/04/a-raspberry-pi-data-logger-for-about-25-temperature-probe-and-thingspeak
I have build my own Pi Zero temperature logger with two DS18B20 Temperature sensors

Build upon Raspbian Lite OS

First:

$ sudo nano /boot/config.txt

Go to the bottom of the file, and add this line:

dtoverlay=w1-gpio

$ sudo reboot

Second:

$ sudo modprobe w1-gpio 

$ sudo modprobe w1-therm


import the Python script and edit the crontab:

sudo crontab -e

-- add the following line at the bottom

*/10 * * * * * python /home/pi/KeikoTemp.py

sudo reboot
