const { By } = require("selenium-webdriver");
const { Widget, Locator } = require("../../../helpers/functional");
const DashboardPage = require("./pages/DashboardPage.locator");

class Dashboard extends DashboardPage {
	constructor(pWebdriver) {
		super(pWebdriver, {}, new Locator(By.id("page"), null));
	}

	async get() {
		await this.webdriver.get("http://127.0.0.1:9090/JODA/Site/Dashboard.html");
	}
}

module.exports = Dashboard;