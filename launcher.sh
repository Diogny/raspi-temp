#!/bin/sh
# launcher.sh
# navigate to home directory, then to this directory, then execute python script, then back home
#https://www.cyberciti.biz/faq/linux-find-out-raspberry-pi-gpu-and-arm-cpu-temperature-command/
#https://www.instructables.com/id/Raspberry-Pi-Launch-Python-script-on-startup/
#https://www.thegeekstuff.com/2009/06/15-practical-crontab-examples/
# make the launcher script an executable:
#	chmod 755 share/php/temp/launcher.sh
# test:
#	sh share/php/temp/launcher.sh
#
#	sudo crontab -e
# add:
#	* * * * * sh /home/pi/share/php/temp/launcher.sh >/home/pi/share/php/temp/py/temps.log 2>&1

cd /
cd home/pi/share/php/temp/py
sudo python3 temps.py
cd /
