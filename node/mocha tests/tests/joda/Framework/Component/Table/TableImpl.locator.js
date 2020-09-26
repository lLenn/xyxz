const { By } = require("selenium-webdriver");
const { Widget, Component, Locator } = require("../../../../../helpers/functional");
const TableBase = require("./TableBase.locator.js");
const TableListBase = require("./TableListBase.locator.js");
const Paging = require("./Paging.locator.js");

class TableImpl extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, "nixps-table.TableImpl", pOptions, pLocator);

		this.pagingTop = new Paging(pWebdriver, {}, new Locator(By.css(".top-paging.nixps-table-Paging"), this.locator));
		this.pagingBottom = new Paging(pWebdriver, {}, new Locator(By.css(".bottom-paging.nixps-table-Paging"), this.locator));
		if(pOptions.renderAs == null || pOptions.renderAs === "table") {
			this.base = new TableBase(pWebdriver, {}, new Locator(By.css(".table-container.nixps-table-TableBase"), this.locator));
		} else if(pOptions.renderAs === "list") {
			this.base = new TableListBase(pWebdriver, {}, new Locator(By.css(".table-container.nixps-table-TableListBase"), this.locator));
		}
	}
}

module.exports = TableImpl;