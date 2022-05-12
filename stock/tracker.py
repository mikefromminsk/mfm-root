import csv
from lxml import etree
import sys
import os

reader = csv.reader(open("s.csv", encoding="utf8"))

events = "function track(params) {\n"
events += "    params.t = new Date().getTime()\n"
events += "    params.token = token\n"
events += "    var xhr = new XMLHttpRequest()\n"
events += "    xhr.open('POST', 'api/track.php\', true)\n"
events += "    xhr.setRequestHeader('Content-Type', 'application/json')\n"
events += "    xhr.send(JSON.stringify(params))\n"
events += "}\n"


def to_camel_case(snake_str):
    components = snake_str.split('_')
    return components[0] + ''.join(x.title() for x in components[1:])


for row in reader:
    if row[1]:
        events += "\n"
        events += "function " + to_camel_case("track_" + row[1]) + "("
        hasParameters = False
        for i in range(2, len(row)):
            if row[i]:
                hasParameters = True
                events += row[i] + ", "
        if hasParameters:
            events = events[:-2]
        events += ") {\n"
        events += "    track({e: '" + row[1] + "', "
        for i in range(2, len(row)):
            if row[i]:
                events += "p" + str(i - 1) + ": " + row[i] + ", "
        events = events[:-2]
        events += "})\n}\n"

file = open("tracker.js", "w", encoding="utf8")
file.write(events)
file.close()