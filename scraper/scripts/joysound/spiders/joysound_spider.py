# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest
from joysound.items import JoysoundItem
import re


class JoysoundSpider(scrapy.Spider):
    name = 'joysound'
    allowed_domains = ['www.joysound.com']
    def __init__(self, user, month, *args, **kwargs):
        super(JoysoundSpider, self).__init__(*args, **kwargs)
        self.start_urls = ['https://www.joysound.com/utasuki/userpage/gp/index.htm?usr=' + user + '&month=' + month + '&startIndex=0&orderBy=0&sortOrder=asc#history']

    def start_requests(self):
            yield SplashRequest(self.start_urls[0], self.parse,
                                args={'wait': 0.5},
                                    )
    def parse(self, response):
        month = re.search(r'month=\d+', response.url).group()[len("month="):]
        for row in response.xpath('//*[@id="historyTabPanel"]/div/div[5]/ul/li'):
            item = JoysoundItem()
            item['month'] = month
            item['rank'] = row.xpath('div[1]/a/div/p[1]/span[1]/strong//text()').extract_first()[:-1]
            item['population'] = row.xpath('div[1]/a/div/p[1]/span[1]/var//text()').extract_first()
            item['score'] = row.xpath('div[1]/a/div/p[1]/span[2]/var//text()').extract_first()
            item['title'] = row.xpath('div[1]/a/div/p[2]/var[1]//text()').extract_first()
            item['artist'] = row.xpath('div[1]/a/div/p[2]/var[2]//text()').extract_first()
            yield item
        if len(response.xpath('//*[@id="historyTabPanel"]/div/div[5]/ul/li')) == 20:
            start_index = re.search(r'startIndex=\d+', response.url).group()
            new_index = "startIndex=" + format(int(start_index[len("startIndex="):])+20, '#0')
            next_url = re.sub(r'startIndex=\d+', new_index, response.url)
            yield SplashRequest(next_url, self.parse, args={'wait': 0.5})
