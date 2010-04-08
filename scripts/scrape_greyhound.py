#/usr/bin/env python
#scrape greyhound's website
#call like: python scrape_greyhound.py Pittsburgh PA "New York" NY 3 23
# python scrape_greyhound.py (from city name in quotes for spaces) (city state)
# (to city name in quotes) (month) (date)
import mechanize
import urllib
import re
import sys



br = mechanize.Browser()
url = "http://www.greyhound.com/home/"
br.open(url).get_data()
#origLoc = "Charlotte"
#origState = "NC"
#destLoc = "New+York"
#destState = "NY"
#month = "3"
#day = "20"

#urlize location
origLoc = sys.argv[1].replace(" ", "+")
origState = sys.argv[2]
#urlize location
destLoc = sys.argv[3].replace(" ", "+")
destState = sys.argv[4]
month = sys.argv[5]
day = sys.argv[6]
url2 = "https://www.greyhound.com/farefinder/step2.aspx?Redirect=Y&Version=1.0&OriginID=151580&OriginCity=%s&OriginState=%s&DestinationID=151239&DestinationCity=%s&DestinationState=%s&Children=0&Legs=1&Adults=1&Seniors=0&DYear=110&DMonth=%s&DDay=%s&DHr=" % (origLoc, origState, destLoc, destState, month, day)
#url2 = "https://www.greyhound.com/farefinder/step2.aspx?Redirect=Y&Version=1.0&OriginID=151580&OriginCity=%s&OriginState=%s&DestinationCity=%s&DestinationState=%s&Children=0&Legs=1&Adults=1&Seniors=0&DYear=110&DMonth=%s&DDay=%s&DHr=" % (origLoc, origState, destLoc, destState, month, day)
#backup
#url2 = "https://www.greyhound.com/farefinder/step2.aspx?Redirect=Y&Version=1.0&OriginCity=%s&OriginState=%s&DestinationCity=%s&DestinationState=%s&Children=0&Legs=1&Adults=1&Seniors=0&DYear=110&DMonth=%s&DDay=%s&DHr=" % (origLoc, origState, destLoc, destState, month, day)
data = br.open(url2).get_data()
#print data
l = re.findall("\$(\d*\.\d*)", data)
if len(l) == 0:
	print "bad"
else:
	print l[0]