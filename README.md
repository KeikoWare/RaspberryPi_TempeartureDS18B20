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

Test that it's working with the temp sensors

    $ cd /sys/bus/w1/devices/
    $ ls
    28-000004e1a947  28-000004ef4b1b  w1_bus_master1
    $ cat 28-000004e1a947/w1_slave
    $  cat 28-000004e1a947/w1_slave
    70 01 4b 46 7f ff 10 10 e1 : crc=e1 YES
    70 01 4b 46 7f ff 10 10 e1 t=23000
    $ cat 28-000004ef4b1b/w1_slave
    50 05 4b 46 7f ff 0c 10 1c : crc=1c YES
    50 05 4b 46 7f ff 0c 10 1c t=85000
IT WORKS :)

Import the Python script and edit the crontab:

    $ wget https://raw.githubusercontent.com/KeikoWare/RaspberryPi_TempeartureDS18B20/master/KeikoTemp.py

Schedule the pi to report to webservice every 10 minutes:

    $ sudo crontab -e

Add the following line at the bottom

    */10 * * * * python /home/pi/KeikoTemp.py

    $ sudo reboot
