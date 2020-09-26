class Content {
	addVariable(pName, pValue) {
		this[pName] = pValue;
	}

	getVariable(pName) {
		return this[pName];
	}
}

module.exports = new Content();