#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
#  temps.py
#  monitor gpu and cpu temperatures and store it in a queue file every minute
#  nano share/py/temps.py
#  python3 share/py/temps.py

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
				#print( 'updating current temperature' )
				#process data to queue
				index = int(data[1])
				queue = data[2].split('|')
				
				#print temperature in order
				
				#start after index
				nextIndex = 0 if index >= (entries - 1) else index + 1
				#curr = nextIndex
				#while curr != index:
				#	print( queue[curr] )
				#	curr = 0 if curr >= (entries - 1) else curr + 1
				#print last one, the current temp
				#print(queue[curr])
				
				#advance index
				index = nextIndex
				
		#else:
		#	#store first temp in index = 0
		#	#print( 'initializing temp data storage' )
	
		#store temp in next queue entry
		queue[index] = cpu_temp_str 
		
		#save
		f = open(filename, "w")
		#print ("saving data file")
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

