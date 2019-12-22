# raspi-temp
Raspberry Pi 3 Temperature logger

# Setup raspi

> Edit **wpa_supplicant.conf** with your own Wi-Fi SSID and password. And copy this **wpa_supplicant.conf** and **ssh** files to the SD card root

```ini
country=US
ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1

network={
	scan_ssid=1
	ssid="SSID"
	psk="password"
}
```

> Log in to your raspi through **Putty** and change password, hot name, set locale settings and time zone,expand **Debian** file system, enable interfaces, etc.

> Make raspi IP static

```bash
sudo nano /etc/dhcpcd.conf
```

Add to the end this, and modify it to your needs before editing:

```ini
interface wlan0

   static ip_address=192.168.1.201/24
   static routers=192.168.1.1
   static domain_name_servers=192.168.1.1
```

# Install Python

```bash
sudo apt install python3 python3-venv
# install pip3 too if needed
dpkg --get-selections | grep python3
sudo apt-get install python3-pip -y
pip3 list
```

# Install Samba
> Easy to share files with **PC**, so you can edit the archives in your **raspi** from your PC with the editor or your like.

```bash
sudo apt-get install samba samba-client cifs-utils

mkdir ~/share
sudo chmod -R 777 /home/pi/share
```

Edit config file and add/modify to match bellow:

	sudo nano /etc/samba/smb.conf

```ini
#In the PC you'll see the [pishare] folder
[PiShare]
   comment=Raspberry Pi Share
   path=/home/pi/share
   browseable=yes
   writeable=yes
   only guest=no
   create mask=0777
   directory mask=0777
   public=no
```

Add **pi** user to **samba** with same password as **pi**:

	sudo smbpasswd -a pi
	sudo service smbd restart

# Install Apache and PHP

	sudo apt install apache2
Give access right to apache files:

	sudo chown -R pi:www-data /var/www/html/
	sudo chmod -R 770 /var/www/html/

Check apache is working:

	wget -O check_apache.html http://127.0.0.1
	cat ./check_apache.html

So now **/var/www/html** is the root of your websites.

Backup old index.html if needed:

	sudo mv /var/www/html/index.html /var/www/html/index.old.html
	ls /var/www/html/

> You can browse this link **http://192.168.1.201/index.old.html** to see the old apache **index.html** file. Now proceed to install **PHP**

	sudo apt install php php-mbstring

Remove index.html

	sudo rm /var/www/html/index.html

Create new index.php

	echo "<?php phpinfo ();?>" > /var/www/html/index.php

# Set up Temperature Logger Website

> Copy your website to **share/php/temp** folder, should have this structure.

```bash
#Create website source folder

mkdir share/php/temp

pi@raspberry:~ $ tree share/php/temp
share/php/temp
├── css
├── favicon.ico
├── get_temp.php
├── img
├── index.html
├── index.php
├── js
│   └── app.js
├── launcher.sh
├── libs
│   ├── bootstrap
│   │   ├── css
│   │   │   ├── bootstrap.css
│   │   │   ├── bootstrap.css.map
│   │   │   ├── bootstrap-grid.css
│   │   │   ├── bootstrap-grid.css.map
│   │   │   ├── bootstrap-grid.min.css
│   │   │   ├── bootstrap-grid.min.css.map
│   │   │   ├── bootstrap.min.css
│   │   │   ├── bootstrap.min.css.map
│   │   │   ├── bootstrap-reboot.css
│   │   │   ├── bootstrap-reboot.css.map
│   │   │   ├── bootstrap-reboot.min.css
│   │   │   └── bootstrap-reboot.min.css.map
│   │   └── js
│   │       ├── bootstrap.bundle.js
│   │       ├── bootstrap.bundle.js.map
│   │       ├── bootstrap.bundle.min.js
│   │       ├── bootstrap.bundle.min.js.map
│   │       ├── bootstrap.js
│   │       ├── bootstrap.js.map
│   │       ├── bootstrap.min.js
│   │       └── bootstrap.min.js.map
│   ├── canvasjs
│   │   ├── canvasjs.min.js
│   │   ├── canvasjs.react.js
│   │   ├── instruction.txt
│   │   └── jquery.canvasjs.min.js
│   └── jquery
│       ├── jquery-3.4.1.min.js
│       ├── jquery-3.4.1.min.map
│       └── jquery-migrate-3.1.0.js
├── LICENSE
├── new features.txt
├── py
│   └── temps.py
├── README.md
├── ssh
└── wpa_supplicant.conf
```

> Everytime you update your source folder files in **share/php/temp** you should copy to the **Apache PHP** folder with:

```bash
	#One time
	mkdir /var/www/html/temp
	cp share/php/temp/favicon.ico /var/www/html/temp/favicon.ico
	
	#Every time you update files
	cp -r -v share/php/temp/libs/ /var/www/html/temp/
	cp -r -v share/php/temp/js/ /var/www/html/temp/
	cp -r -v share/php/temp/css/ /var/www/html/temp/
	cp -r -v share/php/temp/img/ /var/www/html/temp/

	cp share/php/temp/index.php /var/www/html/temp/index.php
	cp share/php/temp/get_temp.php /var/www/html/temp/get_temp.php
	cp share/php/temp/index.html /var/www/html/temp/index.html
```

> Follow instructions in **launcher.sh** to execute service every minute. This service calls **share/php/temp/py/temps.py** Python file, and it updates the file **share/php/temp/py/temps.txt** with temperatures every minuite.

> Next step is to start fan when temperature is over a threshold.