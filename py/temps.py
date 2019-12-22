#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
#  temps.py
#  monitor gpu and cpu temperatures and store it in a queue file every minute
#  nano share/php/temp/py/temps.py
#  python3 share/php/temp/py/temps.py

def main(args):
	try:
		pathname = os.path.dirname(sys.argv[0])
		abspath = os.path.abspath(pathname)
		filename = os.path.join( abspath, "temps.txt")
		
		index = 0
		entries = 60
		queue = [''] * entries # ['' for i in range(60)]
		data = ""
		cpu_temp = float(os.popen("vcgencmd measure_temp | egrep -o '[0-9]*\.[0-9]*'").readline())
		#round(float(os.popen("cat /sys/class/thermal/thermal_zone0/temp").readline()) / 1000, 1)
		cpu_temp_str = "{:.1f}".format(cpu_temp) #str(id)
		
		tm = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		print(tm + ' ' + cpu_temp_str + '°C')
		
		if os.path.exists(filename):
			#read file data
			data = open(filename, "r").read().splitlines()
			if len(data) != 4:
				print( 'wrong temp data storage, reseting...' )
			else:
				#updating current temperature
				#process data to queue
				index = int(data[1])
				queue = data[2].split('|')
				
				#start after index
				nextIndex = 0 if index >= (entries - 1) else index + 1
				
				#advance index
				index = nextIndex
	
		#store temp in next queue entry
		queue[index] = cpu_temp_str 
		
		#save data file
		f = open(filename, "w")
		#current temp
		f.write( cpu_temp_str + '\n' )
		#current queue index
		f.write( str(index) + '\n' )
		#store temp queue data
		f.write( '|'.join(queue) + '\n' )
		#store time
		f.write( tm + '\n' )
		#close file
		f.close()
		
		return 0
	except:
		print( 'exception raised' )
		return 1

if __name__ == '__main__':
	import sys, os, platform
	from datetime import datetime
	
	sys.exit(main(sys.argv))
