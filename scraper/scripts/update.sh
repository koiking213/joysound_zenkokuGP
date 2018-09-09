#!/bin/bash -ue

cd /scripts
: > gp_update.sql

month_list=`date "+%Y%m"`
python3 save_data.py

# 月初めは先月のデータも更新
if [ `date "+%d"` == "01" ]; then
    python3 save_data.py last
    month=`date "+%Y%m"`
    if ((month%100==1)); then
        ((month+=11-100))
    else
        ((month--))
    fi
    month_list+=$month
fi

for month in $month_list
do
    echo "delete from gp_score where month=$month;"
    for user in `cat /userlist | cut -d' ' -f1`
    do
        file=${user}_${month}.csv
        cat << EOF
load data local infile '$file'
into table gp_score
fields terminated by ',' lines terminated by '\n'
(@artist, @month, @population, @rank, @score, @title)
SET
    artist=@artist,
    month=@month,
    population=@population,
    rank=@rank,
    score=@score,
    title=@title,
    user='$user';

EOF
    done
done >> gp_update.sql

mysql -h db -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < gp_update.sql

mv *.csv /data
