import csv
from lxml import etree
import sys
import os

reader = csv.reader(open("s.csv", encoding="utf8"))

strings = []
languages = []

is_first_line = True
for row in reader:
    if is_first_line:
        is_first_line = False
        for i in range(1, len(row)):
            languages.append(row[i])
            strings.append("var str = {\n")
    else:
        for i in range(1, len(row)):
            strings[i - 1] += row[0] + ": '" + row[i] + "',\n"

for i in range(0, len(languages)):
    file = open(languages[i] + ".js", "w", encoding="utf8")
    file.write(strings[i] + "}")
    file.close()