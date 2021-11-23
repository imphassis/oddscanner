import csv
import time

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
    def __init__(self, URL, fileName, range):
        self.URL = URL
        self.rows = []
        self.fileName = fileName
        self.range = range

    def scrapRows(self):
        # Open the URL with the driver and scrap the table's rows
        try:
            driver.get(self.URL)
            tbody = driver.find_element(By.TAG_NAME, "tbody")
            rows = tbody.find_elements(By.TAG_NAME, "tr")
            self.rows = rows

        except Exception as e:
            now = str(time.strftime("%Y%m%d%H%M%S"))
            with open(f"error_{now}.txt", "a") as f:
                f.write(str(e))
            print("\nAn Error occurred, the log was saved into error.txt")
            return None

    @staticmethod
    def getPricePerRow(row):
        # Gets the price and date from the row
        date = row.find_elements(By.TAG_NAME, "td")[0].text
        price = row.find_elements(By.TAG_NAME, "td")[4].text
        return [date, price]

    def getPricesByRange(self):
        array = self.rows[0 : self.range]
        # Iterates over the rows and gets the price for the given range
        data = list(map(self.getPricePerRow, array))
        return data

    @staticmethod
    def saveDataToCSV(data, file):
        # Creating a csv file in write mode
        with open(file, "w") as csv_file:
            writer = csv.writer(csv_file)
            writer.writerow(["Date", "Price"])
            writer.writerows(data)

    def getData(self):
        self.scrapRows()
        data = self.getPricesByRange()
        self.saveDataToCSV(data, self.fileName)
        # verify if self.row is not empty
        if not self.rows:
            print("\nNo data was found, please check the URL")
            return None
        elif self.rows:
            print(f"\nData was successfully scraped, saved in {self.fileName}")

    def printFileName(self):
        print(self.fileName)


currencies = FinanceScraper(EXAMPLE_URL, "eur_btc_rates.csv", 10)

currencies.getData()
