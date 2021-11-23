import csv

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

# Defining webdriver options

options = Options()
options.add_experimental_option("excludeSwitches", ["enable-logging"])
options.headless = True
s = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=s, options=options)


EXAMPLE_URL = "https://finance.yahoo.com/quote/BTC-EUR/history/"


class FinanceScraper:
    def __init__(self, URL):
        self.URL = URL
        self.rows = []

    def scrapRows(self):
        try:
            driver.get(self.URL)
            tbody = driver.find_element(By.TAG_NAME, "tbody")
            rows = tbody.find_elements(By.TAG_NAME, "tr")
            self.rows = rows
        except Exception as e:
            print(e)
            return None

    def getDateAndPrice(self, dateIndex, datePrice):
        # 0, 4
        date = self.row.find_elements(By.TAG_NAME, "td")[dateIndex].text
        price = self.row.find_elements(By.TAG_NAME, "td")[datePrice].text
        return [date, price]

    def saveDataToCSV(data, filename):
        # Creating a csv file in write mode
        with open(filename, "w") as csv_file:
            writer = csv.writer(csv_file)
            writer.writerow(["Date", "Price"])
            writer.writerows(data)

    def pricesByRange(self, rangeStart, rangeEnd):
        history = list(map(self.getDateAndPrice, self.rows[rangeStart:rangeEnd]))
        self.rows = self.scrapRows(URL)

    def getData(self):
        self.rows = self.scrapRows(URL)
        self.saveDataToCSV(tenDaysHistory, "eur_btc_rates.csv")
        print("Done")
