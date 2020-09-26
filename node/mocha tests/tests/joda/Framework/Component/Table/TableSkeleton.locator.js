const { By } = require("selenium-webdriver");
const { Widget, Component, ComponentList, Locator } = require("../../../../../helpers/functional");

class TableSkeleton extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, "nixps-table.TableSkeleton", pOptions, pLocator);

		this.rows = new ComponentList(pWebdriver, TableRow, new Locator(By.css("table > tbody > tr"), this.locator));
	}
}

class TableRow extends Component {
	constructor(pWebdriver, pLocator) {
		super(pWebdriver, pLocator);

		this.cells = new ComponentList(pWebdriver, TableCell, new Locator(By.css("td"), this.locator));
	}
}

class TableCell extends Component {
	constructor(pWebdriver, pLocator) {
		super(pWebdriver, pLocator);
	}

	getContent(pClass) {
		return new pClass(this.webdriver, {}, new Locator(By.css(":nth-child(1)"), this.locator))
	}
}

module.exports = { TableRow, TableCell, TableSkeleton };