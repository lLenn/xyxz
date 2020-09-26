const webdriver = require("selenium-webdriver");
const { By } = webdriver;
const { expect } = require("chai");
const _ = require("lodash");
const Locator = require("./Locator.js");
const Component = require("./Component.js");

class Widget extends Component {
	constructor(pWebdriver, pComponent, pComponentOptions, pLocator) {
		if(pWebdriver === undefined) {
			throw new Error(this.constructor.name + ": 'pWebdriver' cannot be undefined!");
		}
		var guid = pWebdriver.generateUID();
		if(pLocator === null || !(pLocator instanceof Locator)) {
			pLocator = new Locator(By.css("." + guid), null);
		}
		super(pWebdriver, pLocator);
		this.guid = guid;
		var componentNamespace = pComponent.split(".");
		if(componentNamespace.length < 2) {
			throw new Error("Widget: Component should include namespace!");
		}
		this.componentNamespace = componentNamespace[0];
		this.componentName = componentNamespace[1];
		this.componentOptions = pComponentOptions;
	}

	async create(pTimeout, pStyle) {
		if(pTimeout === undefined) {
			pTimeout = 10000;
		}
		if(pStyle === undefined) {
			pStyle = true;
		}
		var element = await this.findElements();
		if(element.length === 0) {
			await this.webdriver.executeScript(function(pGUID, pComponentName, pComponentOptions, pStyle) {
				document.hasRendered = false;
				$(document.body).append($("<div>").addClass(pGUID));
				if(pStyle === true) {
					$(document.body).css({ position: "absolute", top: 0, bottom: 0, left: 0, right: 0 });
					$("." + pGUID).css({ margin: "50px" });
				}
				$("." + pGUID).one("rendered", function() { document.hasRendered = true; });
				$("." + pGUID)[pComponentName](pComponentOptions);
			}, this.guid, this.componentName, this.componentOptions, pStyle);
			var that = this;
			return new Promise(async function(pResolve, pReject) {
				var intervalID;
				var timeoutID = setTimeout(function() {
					clearInterval(intervalID);
					pReject("Element hasn't triggered 'rendered' event. Make sure it's implemented or increase the timeout!");
				}, pTimeout)
				intervalID = setInterval(async function() {
					var rendered = await that.webdriver.executeScript(function() { return document.hasRendered; });
					if(rendered === true) {
						clearInterval(intervalID);
						clearTimeout(timeoutID);
						pResolve();
					}
				}, 100);
			});
		}
	}

	async redraw() {
		await this.remove();
		await this.create();
	}

	async remove() {
		await this.webdriver.executeScript(function(pElement) {
			$(pElement).remove();
		}, await this.findElement());
	}

	async executeMethod(methodName, args) {
		return await this.webdriver.executeScript(function(pElement, pComponentName, pMethodName, pArgs) {
			return $(pElement)[pComponentName].apply($(pElement), [pMethodName].concat(pArgs));
		}, await this.findElement(), this.componentName, methodName, args);
	}

	async expectOptionToEqual(pOption, pValue) {
		if(typeof pValue === "object") {
			expect(await this.executeMethod("option", [pOption])).to.deep.equal(pValue);
		} else {
			expect(await this.executeMethod("option", [pOption])).to.equal(pValue);
		}
	}

	async expectOptionCloseTo(pOption, pValue) {
		expect(await this.executeMethod("option", [pOption])).closeTo(pValue, 0.00000000000001);
	}

	async isWidget() {
		await this.isLocated();
		await this.expectToHaveTagName("div");
		await this.expectToHaveClass(this.componentNamespace + "-" + this.componentName);
	}

	async getOption(pOption, pTranslateFunction) {
		if(pTranslateFunction === undefined) {
			pTranlateFunction = Component.TranslateNoop;
		}
		return this.webdriver.executeScript(function(pElement, pComponentNamespace, pComponentName, pOptionName, pTranslate) {
			if($(pElement).data(pComponentNamespace + "-" + pComponentName) != null) {
				var translateFunction = new Function("return " + pTranslate)();
				return translateFunction($(pElement)[pComponentName]("option", pOptionName));
			}
		}, await this.findElement(), this.componentNamespace, this.componentName, pOption, pTranslateFunction);
	}

	async hasOption(pOption, pValue, pTranslateFunction, pCompare) {
		if(pTranslateFunction === undefined) {
			pTranslateFunction = Component.TranslateNoop;
		}
		if(pCompare === undefined) {
			pCompare = "isEqual"
		}
		let that = this;
		return new Promise((resolve, reject) => {
			let intervalCount = 0
			let intervalID = setInterval(async function() {
				let option;
				let valid;
				try {
					option = await that.getOption(pOption, pTranslateFunction);
					valid = _[pCompare](option, pValue);
				} catch(e) {
					clearInterval(intervalID);
					reject(e)
				}

				if(valid === false && intervalCount++ >= 30) {
					clearInterval(intervalID);
					reject("Couldn't navigate to ComponentLayoutHierachy!");
				} else if(valid === true){
					clearInterval(intervalID);
					resolve();
				}
			}, 100);
		})
	}

	async getInnerVariable(pVariable, pTranslateFunction) {
		if(pTranslateFunction === undefined) {
			pTranlateFunction = Component.TranslateNoop;
		}
		return this.webdriver.executeScript(function(pElement, pComponentNamespace, pComponentName, pVariableName, pTranslate) {
			var widget = $(pElement).data(pComponentNamespace + "-" + pComponentName);
			if(widget != null) {
				var translateFunction = new Function("return " + pTranslate)();
				return translateFunction(widget[pVariableName]);
			}
		}, await this.findElement(), this.componentNamespace, this.componentName, pVariable, pTranslateFunction);
	}

	async hasInnerVariable(pVariable, pValue, pTranslateFunction, pCompare) {
		if(pTranslateFunction === undefined) {
			pTranlateFunction = Component.TranslateNoop;
		}
		if(pCompare === undefined) {
			pCompare = "isEqual"
		}
		let that = this;
		return new Promise((resolve, reject) => {
			let intervalCount = 0
			let intervalID = setInterval(async function() {
				let variable;
				let valid;
				try {
					variable = await that.getInnerVariable(pVariable, pTranslateFunction);
					valid = _[pCompare](variable, pValue);
				} catch(e) {
					clearInterval(intervalID);
					reject(e)
				}

				if(valid === false && intervalCount++ >= 30) {
					clearInterval(intervalID);
					reject("Couldn't navigate to ComponentLayoutHierachy!");
				} else if(valid === true){
					clearInterval(intervalID);
					resolve();
				}
			}, 100);
		})
	}
}

Widget.IsWidget = function() {
	return true;
}

module.exports = Widget;