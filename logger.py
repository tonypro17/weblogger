import smbus
import time
import datetime
import sqlite3
import sys

bus = smbus.SMBus(1)
address = 0x4b

def temperature():
        rvalue0 = bus.read_word_data(address,0)
        rvalue1 = (rvalue0 & 0xff00) >> 8
        rvalue2 = rvalue0 & 0x00ff
        rvalue = (((rvalue2 * 256) + rvalue1) >> 4 ) *.0625
        return rvalue

try:
	db = sqlite3.connect('temperatures.db')
	
	now = datetime.datetime.now()
	time = now.strftime("%H:%M:%S")
	date = now.strftime("%Y-%m-%d")
	temp = (temperature() * 1.8 + 32)

	cur = db.cursor()
	cur.execute("INSERT INTO temptable (date,time,temperature) VALUES (?,?,?)",(date,time,temp))

	db.commit()
	
except sqlite3.Error, e:
	print "Error %s:" % e.args[0]
	sys.exit(1)

finally:
	
	if db:
		db.close()
