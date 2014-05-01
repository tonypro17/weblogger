
# Author: Tony Provencal
# ECE331 Project 2
# Due May 1, 2014

# Script to fetch temperature data from an attached i2c-connected temperature sensor

import smbus
import time
import datetime
import sqlite3
import sys

# temperature sensor is on i2c bus 1, address 0x4b
bus = smbus.SMBus(1)
address = 0x4b

# read the raw temperature data and convert it to celsius
def temperature():
        rvalue0 = bus.read_word_data(address,0)
        rvalue1 = (rvalue0 & 0xff00) >> 8
        rvalue2 = rvalue0 & 0x00ff
        rvalue = (((rvalue2 * 256) + rvalue1) >> 4 ) *.0625
        return rvalue

# connect to existing sqlite3 database
try:
	db = sqlite3.connect('/home/pi/Desktop/331proj2/temperatures.db')
	
	# organize data to be inserted
	now = datetime.datetime.now()
	time = now.strftime("%H:%M:%S")
	date = now.strftime("%Y-%m-%d")
	# convert temperature data to fahrenheit
	temp = (temperature() * 1.8 + 32)

	# insert data
	cur = db.cursor()
	cur.execute("INSERT INTO temptable (date,time,temperature) VALUES (?,?,?)",(date,time,temp))

	# commit changes
	db.commit()
	
# catch errors
except sqlite3.Error, e:
	print "Error %s:" % e.args[0]
	sys.exit(1)

	# close database
	db.close()
