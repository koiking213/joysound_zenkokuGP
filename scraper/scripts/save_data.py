# -*- coding: utf-8 -*-

import subprocess
import datetime
import argparse
import sys

args = sys.argv
if len(args) == 2:
    if args[1] == "last":
        today = datetime.date.today()
        first = today.replace(day=1)
        last_month = first - datetime.timedelta(days=1)
        months = [last_month.strftime("%Y%m")]
    elif args[1] == "all":
        day = datetime.date.today()
        months = [day.strftime("%Y%m")]
        for i in range(3):
            first = day.replace(day=1)
            day = first - datetime.timedelta(days=1)
            months.append(day.strftime("%Y%m"))
    else:
        print("usage: save_data.py [last|all]")
else:
    months = [datetime.date.today().strftime("%Y%m")]

users = open("/userlist", "r")

for line in users:
    user = line.split()[0]
    encoded_id = line.split()[1]
    for month in months:
        output = user + "_" + month + ".csv"
        command = ["scrapy", "crawl", "-o", "-", "-t", "csv",
                   "-a", "user="+encoded_id,
                   "-a", "month="+month,
                   "joysound"]
        with open(output, 'w') as f:
            subprocess.call(command, stdout=f)
