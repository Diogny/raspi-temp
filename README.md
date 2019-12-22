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

```bash
mkdir share/php/temp
#Copy your website to **share/php/temp** folder, should have this structure

share/php/temp
├───css
├───img
├───js
|   └───app.js
├───libs
|   ├───bootstrap
|   |   ├───css
|   |   |   ... files
|   |   └───js
|   |       ... files
|   ├───canvasjs
|   |       ... files
|   └───jquery
|           ... files
├───py
|   
├───favicon.ico
├───get_temp.php
├───index.html
├───index.php
├───LICENSE
├───README.md
├───ssh
└───wpa_supplicant.conf
```

```yaml
{
	
   "this-json": "looks awesome..."
}