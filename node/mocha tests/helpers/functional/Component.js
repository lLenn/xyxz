const { By, until, Button } = require("selenium-webdriver");
const { expect } = require("chai");
const Locator = require("./Locator.js");

class Component {
	constructor(pWebdriver, pLocator) {
		if(pWebdriver === undefined) {
			throw new Error(this.constructor.name + ": 'pWebdriver' cannot be undefined!");
		}
		if(pLocator === undefined) {
			throw new Error(this.constructor.name + ": 'pLocator' cannot be undefined!");
		} else if(!(pLocator instanceof Locator)) {
			throw new Error(this.constructor.name + ": parameter 'pLocator' should be an instance of Locator!")
		}
		this.webdriver = pWebdriver;
		this.locator = pLocator;
	}

	addChild(pName, pComponent, pOptions, pRelativeSelector) {
		if(this[pName] === undefined) {
			this[pName] = new pComponent(this.webdriver, pOptions, new Locator(By.css(pRelativeSelector), this.locator));
		} else {
			throw new Error("Can't define child on Component!");
		}
	}

	async findElements() {
		var that = this;
		return this.locator.iterateParents(async function(pAccumulator, pLocator, pIndex) {
			if(pIndex === 0) {
				pAccumulator = await that.webdriver.findElements(pLocator, Component.WAIT);
			} else {
				var childElements = []
				for(var i = 0, len = pAccumulator.length; i < len; i++) {
					childElements.concat(await pAccumulator[i].findElements(pLocator, Component.WAIT));
				}
				pAccumulator = childElements;
			}
			return pAccumulator;
		}, []);
	}

	async findElement() {
		var that = this;
		return this.locator.iterateParents(async function(pAccumulator, pLocator, pIndex) {
			if(pIndex === 0) {
				pAccumulator = await that.webdriver.findElement(pLocator, Component.WAIT);
			} else {
				pAccumulator = await pAccumulator.findElement(pLocator, Component.WAIT);
			}
			return pAccumulator;
		});
	}

	findRoot() {
		var that = this;
		var rootBy = this.locator.getRootBy();
		return new Promise(async function(resolve, reject) {
			try {
				var element = await that.webdriver.findElement(rootBy, Component.WAIT);
				resolve(element);
			} catch(e) {
				reject(e);
			}
		});
	}

	async isLocated() {
		var that = this;
		return this.locator.iterateParents(async function(pAccumulator, pLocator, pIndex) {
			if(pIndex === 0) {
				pAccumulator = await that.webdriver.wait(until.elementLocated(pLocator), Component.WAIT);
			} else {
				pAccumulator = await pAccumulator.wait(until.elementLocated(pLocator), Component.WAIT);
			}
			return pAccumulator;
		});
	}

	async isVisible() {
		return this.webdriver.wait(until.elementIsVisible(await this.isLocated()), Component.WAIT);
	}

	async isNotVisible() {
		return this.webdriver.wait(until.elementIsNotVisible(await this.isLocated()), Component.WAIT);
	}

	async expectEventToFire(instructions, eventName, expectedData, translateFunction) {

		await this.addEventListener(eventName);
		await this.executeAction(instructions);
		expect(await this.getEventData(eventName, translateFunction)).to.deep.equal(expectedData);
		await this.removeEventListener(eventName);
	}

	async expectEventNotToFire(instructions, eventName) {
		await this.addEventListener(eventName);
		await this.executeAction(instructions);
		expect(await this.getEventData(eventName)).to.equal(null);
		await this.removeEventListener(eventName);
	}

	async addEventListener(eventName) {
		await this.webdriver.executeScript(function(pElement, pEventName) {
			window[pEventName] = null;
			$(pElement).on(pEventName, function(event, data) { window[pEventName] = data });
		}, this.findRoot(), eventName);
	}

	async getEventData(eventName, translateFunction) {
		if(translateFunction === undefined) {
			translateFunction = Component.TranslateNoop;
		}

		return this.webdriver.executeScript(function(pEventName, pTranslateFunction) {
			var translate = new Function("return " + pTranslateFunction + ";")();
			return translate(window[pEventName]);
		}, eventName, translateFunction);
	}

	async removeEventListener(eventName) {
		await this.webdriver.executeScript(function(pElement, pEventName) {
			delete window[pEventName];
			$(pElement).off(pEventName, function(event, data) { window[pEventName] = data });
		}, this.findRoot(), eventName);
	}

	async expectToHaveTagName(pTagName) {
		var tagName = await (await this.findElement()).getTagName();
		expect(tagName).to.equal(pTagName);
	}

	async expectToHaveClass(pClassName) {
		var className = await (await this.findElement()).getAttribute("class");
		var match = className.match(new RegExp(pClassName));
		expect(match).to.not.be.null;
		expect(match).to.have.length(1);
	}

	async getRect() {
		var rect = await(await this.findElement()).getRect();
		rect.x = rect.x.toFixed(0);
		rect.y = rect.y.toFixed(0);
		rect.width = rect.width.toFixed(0);
		rect.height = rect.height.toFixed(0);
		return rect;
	}

	async getDimension() {
		var offset = await this.jQueryMethod("offset");
		return {
			x: offset.left,
			y: offset.top,
			width: await this.jQueryMethod("width"),
			height: await this.jQueryMethod("height"),
			innerWidth: await this.jQueryMethod("innerWidth"),
			innerHeight: await this.jQueryMethod("innerHeight"),
			outerWidth: await this.jQueryMethod("outerWidth"),
			outerHeight: await this.jQueryMethod("outerHeight")
		}
	}

	async jQueryMethod(pMethod, pArgs) {
		return await this.webdriver.executeScript(function(pElement, pMethod, pArgs) {
			return $(pElement)[pMethod].apply($(pElement), pArgs);
		}, await this.findElement(), pMethod, pArgs);
	}

	async expectToHaveRect(pRect) {
		expect(await this.rect()).to.equal(pRect);
	}

	async attr(pAttr) {
		return await (await this.findElement()).getAttribute(pAttr);
	}

	async expectToHaveAttr(pAttr, pValue) {
		expect(await this.attr(pAttr)).to.equal(pValue);
	}

	async expectToNotHaveAttr(pAttr) {
		expect(await this.attr(pAttr)).to.be.null;
	}

	async text(pValue) {
		return await (await this.findElement()).getText(pValue)
	}

	async expectToHaveText(pValue) {
		expect(await this.text()).to.equal(pValue);
	}

	async css(pKey, pStyle) {
		if(pStyle === undefined) {
			return await (await this.findElement()).getCssValue(pKey);
		} else {
			await this.webdriver.executeScript(function(pElement, pKey, pStyle) {
				$(pElement).css(pKey, pStyle);
			}, await this.findElement(), pKey, pStyle);
		}
	}

	async expectToHaveStyle(pStyle, pValue) {
		var that = this;
		var expectStyle = async function(key, value) {
			var styleValue = await that.css(key);
			expect(styleValue).to.equal(value);
		}
		if(typeof pStyle === "object") {
			for(var prop in pStyle) {
				await expectStyle(prop, pStyle[prop]);
			}
		} else {
			await expectStyle(pStyle, pValue);
		}
	}

	async fillIn(pValue) {
		var element = await this.isVisible();
		await element.clear();
		await element.sendKeys(pValue);
	}

	async selectOption(pValue) {
		await this.isVisible();
		await this.click();
		let option = new Component(this.webdriver, new Locator(By.css("option[value='" + pValue + "']"), this.locator));
		await (await option.findElement()).click();
	}

	async click(pMoveOptions) {
		await this.executeAction(await this.createInstructions("click", pMoveOptions));
	}

	async contextClick(pMoveOptions) {
		await this.executeAction(await this.createInstructions("contextClick", pMoveOptions));
	}

	async dragAndDrop(pMoveOptions) {
		await this.executeAction(await this.createInstructions("dragAndDrop", pMoveOptions));
	}

	async hover() {
		await this.executeAction(await this.createInstructions("move"));
	}

	async scrollIntoView() {
		await this.webdriver.executeScript(function(pElement) {
			pElement.scrollIntoView(true);
		}, await this.findElement());
	}

	async executeAction(instructions) {
		try {
			let tries = 0;
			while(await this.isVisible() === false && tries < 100) {
				await this.scrollIntoView();
				tries++;
			}
			if(await this.isVisible() === false) {
				throw new Error("Element is not clickable!");
			}
			await instructions.actions.perform();
		} catch(e) {
			if(e.message.indexOf("Unrecognized command: actions") !== -1 || e.name === "UnknownCommandError") {
				if(instructions.type === "click") {
					await this.findElement().click();
				} else {
					e.message = "Command 'actions' is not supported by Chrome. Please run firefox instead.";
					throw e;
				}
			} else {
				throw e;
			}
		}
	}

	async createInstructions(pType, pMoveOptions) {
		let element = await this.findElement();
		if(pMoveOptions === undefined) {
			pMoveOptions = { origin: element, x: 0, y: 0 };
		} else if(pMoveOptions.origin === undefined) {
			pMoveOptions.origin = element;
		}
		let xStr = typeof pMoveOptions.x === "string" && ["left", "center", "middle", "right"].indexOf(pMoveOptions.x) !== -1;
		let yStr = typeof pMoveOptions.y === "string" && ["top", "center", "middle", "bottom"].indexOf(pMoveOptions.y) !== -1;
		if(xStr || yStr) {
			let rect = await element.getRect();
			if(xStr) {
				switch(pMoveOptions.x) {
					case "left": pMoveOptions.x = -Math.ceil(rect.width/2); break;
					case "middle":
					case "center": pMoveOptions.x = 0; break;
					case "right": pMoveOptions.x = Math.floor(rect.width/2); break;
				}
				if(typeof pMoveOptions.offsetX === "number") {
					pMoveOptions.x += pMoveOptions.offsetX;
				}
			}
			if(yStr) {
				switch(pMoveOptions.y) {
					case "top": pMoveOptions.y = -Math.ceil(rect.height/2); break;
					case "middle":
					case "center": pMoveOptions.y = 0; break;
					case "bottom": pMoveOptions.y = Math.floor(rect.height/2); break;
				}
				if(typeof pMoveOptions.offsetY === "number") {
					pMoveOptions.y += pMoveOptions.offsetY;
				}
			}
		}
		let instructions = { type: pType, actions: this.webdriver.actions() };
		switch(pType) {
			case "click":
				instructions.actions.move(pMoveOptions).press(Button.LEFT).release(Button.LEFT);
				break;
			case "contextClick":
				instructions.actions.move(pMoveOptions).press(Button.RIGHT).release(Button.RIGHT);
				break;
			case "dragAndDrop":
				instructions.actions.dragAndDrop(element, pMoveOptions);
				break;
			case "move":
				instructions.actions.move(pMoveOptions);
				break;
		}
		return instructions;
	}

	async getData(pName, pTranslateFunction) {
		if(pTranslateFunction === undefined) {
			pTranslateFunction = Component.TranslateNoop;
		}
		return await this.webdriver.executeScript(function(pElement, pDataName, pTranslate) {
			var translateFunction = new Function("return " + pTranslate)();
			return translateFunction($(pElement).data(pDataName));
		}, await this.findElement(), pName, pTranslateFunction);
	}
}

Component.IsComponent = function() {
	return true;
}

Component.TranslateNoop = function(pVariable) {
	return pVariable;
}

Component.TranslateJSON = function(pVariable) {
	return pVariable.toJSON();
}

Component.TranslateArrayJSON = function(pArray){
	return pArray.map(function(pItem) { return pItem.toJSON(); });
}

Component.WAIT = 2000;

module.exports = Component;