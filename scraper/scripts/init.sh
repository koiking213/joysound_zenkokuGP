#!/bin/bash -ue

cd /scripts
: > gp_init.sql

python3 save_data.py all
mv *.csv /data

for user in `cat /userlist | cut -d' ' -f1`
do
    for file in `ls /data/${user}*.csv`
    do
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
done >> gp_init.sql

mysql -h db -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < gp_init.sql

