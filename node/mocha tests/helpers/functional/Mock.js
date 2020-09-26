const webdriver = require("selenium-webdriver");
const { expect } = require("chai");

class Mock {
	constructor(pWebdriver, pCall, pResponse) {
		this.webdriver = pWebdriver;
		this.call = pCall;
		this.response = pResponse
	}
	
	async create() {
		if(this.response !== undefined) {
			await this.webdriver.executeScript(function(pCall, pResponse) {
				mockCall(pCall, pCall, pResponse);
			}, this.call, this.response);
		} else {
			await this.webdriver.executeScript(function(pCall) {
				mockCall(pCall, pCall);
			}, this.call);
		}
		return this;
	}
	
	async calledWith() {
		let calls = await this.webdriver.executeScript(function(pCall) {
			return call_executions[pCall];
		}, this.call);
		expect(calls).to.have.lengthOf.at.least(1);
		expect(calls.pop().args).to.deep.equal(Array.from(arguments));
	}
	
	async undo() {
		await this.webdriver.executeScript(function(pCall) {
			undoMock(pCall, pCall.replace(/[.]+/g, "-"));
		}, this.call);
	}
}

module.exports = Mock;