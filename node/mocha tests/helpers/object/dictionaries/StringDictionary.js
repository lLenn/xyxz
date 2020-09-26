const TypeDictionary = require("./TypeDictionary.js");

StringDictionary = function(pVariables) {
	this.typeDictionary();

    this.used = [];
    if(pVariables === undefined) {
    	pVariables = ["lorem", "ipsum", "dolor", "sit", "amet", "consectetur", "adipiscing", "elit", "nunc", "pharetra", "nisl",  "dictum", "vehicula", "praesent", "blandit", "bibendum"];
    }

	this.variableDictionary = pVariables;
}

StringDictionary.prototype = Object.assign({}, TypeDictionary.prototype, {
	constructor: StringDictionary,
    typeDictionary: TypeDictionary,

    getVariable: function() {
        var available = this.variableDictionary.filter((pItem) => { return this.used.indexOf(pItem) === -1; });
        if(available.length > 0) {
            var rnd = Math.floor(Math.random() * available.length);
            this.used.push(available[rnd]);
            return available[rnd];
        } else {
            this.used = [];
            return this.getVariable();
        }
    },

	copy: function() {
		return new StringDictionary([].concat(this.variableDictionary));
	}
});

module.exports = StringDictionary;