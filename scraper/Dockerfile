FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
  && apt-get install -y python3 python3-pip mysql-client tzdata

RUN rm /etc/localtime \
  && ln -s /usr/share/zoneinfo/Asia/Tokyo /etc/localtime


RUN pip3 install scrapy scrapy-splash

ADD scripts /scripts

# TODO: cronにgp_updateを登録