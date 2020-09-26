const { By } = require("selenium-webdriver");

class Locator {
	constructor(pLocator, pParentLocator) {
		if(pLocator === undefined) {
			throw new Error(this.constructor.name + ": 'pLocator' cannot be undefined!");
		} else if(!(pLocator instanceof By)) {
			throw new Error(this.constructor.name + ": parameter 'pLocator' should be an instance of By!")
		}
		if(pParentLocator !== null && !(pParentLocator instanceof Locator)) {
			throw new Error(this.constructor.name + ": parameter 'pParentLocator' should be null or an instance of Locator!")
		}
		this.locator = pLocator;
		this.parentLocator = pParentLocator;
	}

	getParentsBy() {
		let parents = [ this.locator ];
		let allCSS;
		if(this.locator.using === "css selector") {
			allCSS = this.locator.value;
		} else {
			allCSS = false;
		}
		let parentLocator = this.parentLocator;
		while(parentLocator !== null) {
			if(allCSS !== false && parentLocator.locator.using === "css selector") {
				allCSS = parentLocator.locator.value + " " + allCSS;
			} else {
				allCSS = false;
			}
			parents.unshift(parentLocator.locator);
			parentLocator = parentLocator.parentLocator;
		}
		if(allCSS !== false) {
			return [By.css(allCSS)];
		} else {
			return parents;
		}
	}

	getRootBy() {
		let parentLocator = this.parentLocator;
		if(parentLocator !== null) {
			while(true) {
				if(parentLocator.parentLocator === null) {
					return parentLocator.locator;
				}
				parentLocator = parentLocator.parentLocator;
			}
		} else {
			return this.locator;
		}
	}

	async iterateParents(pCallback, pAccumulator) {
		let parents = this.getParentsBy();
		return new Promise(async function(resolve, reject) {
			try {
				var accumulator = parents.reduce(pCallback, pAccumulator);
				resolve(accumulator);
			} catch(e) {
				reject(e);
			}
		});
	}
}

module.exports = Locator;