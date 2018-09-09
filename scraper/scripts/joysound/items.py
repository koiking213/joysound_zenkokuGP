# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# https://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class JoysoundItem(scrapy.Item):
    month = scrapy.Field()
    rank = scrapy.Field()
    population = scrapy.Field()
    score = scrapy.Field()
    title = scrapy.Field()
    artist = scrapy.Field()
