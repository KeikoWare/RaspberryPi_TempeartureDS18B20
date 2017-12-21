#!/usr/bin/python
import os
import glob
import time
import sys
import datetime
import urllib
import urllib2

url = "https://www.keikoware.dk/temp/capture.php"
basedir = '/sys/bus/w1/devices/'
device_folder_arr = glob.glob(basedir + '28*')
device_file = '/w1_slave'

def read_temp_raw():
  f = open(device_folder + device_file, 'r')
  lines = f.readlines()
  f.close
  return lines

def read_temp():
  lines = read_temp_raw()
  while lines[0].strip()[-3:] != 'YES':
    time.sleep(0.2)
    line = read_temp_raw()
  equals_pos = lines[1].find('t=')
  if equals_pos != -1:
    temp_string = lines[1][equals_pos+2:]
    temp_c = float(temp_string)/1000.0
    return temp_c

def getserial():
  # Extract serial from cpuinfo file
  cpuserial = "0000000000000000"
  try:
    f = open('/proc/cpuinfo','r')
    for line in f:
      if line[0:6]=='Serial':
        cpuserial = line[10:26]
    f.close()
  except:
    cpuserial = "test323456789"
  return cpuserial

for device_folder in device_folder_arr:
  temp_in = read_temp()
  sensor = os.path.basename(os.path.normpath(device_folder))
  uid = getserial()
  print("Sensor={0} Temp={1:0.1f}*".format(sensor, temp_in))
  payload = {'sensor':sensor,'temperature':temp_in,'humidity':'0','uid':uid}
  request = urllib2.Request(url,data=urllib.urlencode(payload))
  request.add_header('Content-Type','application/x-www-form-urlencoded')
  response = urllib2.urlopen(request)
