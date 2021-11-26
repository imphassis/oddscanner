import csv
import time

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait
from webdriver_manager.chrome import ChromeDriverManager

options = Options()
options.add_experimental_option("excludeSwitches", ["enable-logging"])
options.headless = True
s = Service(ChromeDriverManager().install())
print("Checking if ChromeDriverManager is installed")
driver = webdriver.Chrome(service=s, options=options)


class FinanceScraper:
    def __init__(self, fileName, range):
        self.URL = "https://finance.yahoo.com/quote/BTC-EUR/history/"
        self.rows = []
        self.fileName = fileName
        self.range = range

    def scrapTableRows(self):
        try:
            print("Opening browser in headless mode...")
            driver.get(self.URL)
            wait = WebDriverWait(driver, 10)
            print("Visiting URL to parse content...")
            tbody = wait.until(EC.presence_of_element_located((By.TAG_NAME, "tbody")))
            rows = tbody.find_elements(By.TAG_NAME, "tr")
            self.rows = rows[0 : self.range]
        except Exception as e:
            self.errorHandler(e)

    @staticmethod
    def getPricePerRow(row):
        date = row.find_elements(By.TAG_NAME, "td")[0].text
        price = row.find_elements(By.TAG_NAME, "td")[4].text
        return [date, price]

    def savePricesToArray(self, array):
        data = list(map(self.getPricePerRow, array))
        return data

    @staticmethod
    def exportDataToCSV(data, file):
        with open(file, "w") as csv_file:
            writer = csv.writer(csv_file)
            writer.writerow(["Date", "Price"])
            writer.writerows(data)

    def errorHandler(e):
        now = str(time.strftime("%Y%m%d%H%M%S"))
        with open(f"error_{now}.txt", "a") as f:
            f.write(str(e))
            print("\nAn Error occurred, the log was saved into error.txt")
            return None

    def main(self):
        self.scrapTableRows()
        data = self.savePricesToArray(self.rows)
        self.exportDataToCSV(data, self.fileName)
        # verify if self.row is not empty
        if not self.rows:
            print("\nNo data was found, please check the URL")
            return None
        elif self.rows:
            print(f"\nData was successfully scraped, saved in {self.fileName}")


currencies = FinanceScraper("eur_btc_rates.csv", 10)  # Filename, range of days

currencies.main()
