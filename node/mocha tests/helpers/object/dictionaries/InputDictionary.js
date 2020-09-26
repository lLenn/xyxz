const TestObject = require("../TestObject.js");
const TypeDictionary = require("./TypeDictionary.js");
const StringDictionary = require("./StringDictionary.js");

InputDictionary = function() {
	this.typeDictionary();

	this.stringDictionary = new StringDictionary();
    this.ids = [];
    this.usedInput = [];
    this.inputDictionary = [{
		type: "Input",
		options: {
			type: "text"
		}
	}, {
		type: "Input",
		options: {
			type: "textarea"
		}
	}, {
		type: "Input",
		options: {
			type: "checkbox",
			checkboxLabel: ""
		}
	}];
}

InputDictionary.prototype = Object.assign({}, TypeDictionary.prototype, {
	constructor: InputDictionary,
    typeDictionary: TypeDictionary,

    getComponent: function() {
    	var input = this._getInput();
		return {
			type: "component",
			element: input
		}
    },

	getField: function() {
		var component = this._getInput();
		return {
			type: "field",
			element: {
				key: this._getUID(),
				label: this.stringDictionary.getVariable(),
				component: input
			}
		}
	},

	getLayout: function(pName) {
		return [{
			content: name,
			width: { md: 12 }
		}]
	},

    _getInput: function() {
        var available = this.inputDictionary.filter((pItem) => { return this.usedInputs.some(function(pUsedItem){ return pUsedItem.type === pItem.type && pUsedItem.optionsType === pItem.options.type }) === false; });
        if(available.length > 0) {
            var rnd = Math.floor(Math.random() * available.length);
            this.usedInputs.push({ type: available[rnd].type, optionsType: available[rnd].options.type });
            var component = TestObject.copy(available[rnd]);
            component.id = this._generateUID;
            return component;
        } else {
            this.used = [];
            return this.getInput();
        }
    },

	_randomLetter() {
	    return letters[Math.floor(Math.random() * 25)];
	},

	_generateUID() {
		let uid;
		do {
			uid = _randomLetter() + _randomLetter() + '-' + _randomLetter() + '-' + _randomLetter() + '-' + _randomLetter() + '-' + _randomLetter() + _randomLetter() + _randomLetter();
		}
		while(this.ids.indexOf(uid) !== -1);
		this.ids.push(uid);
		return uid;
	},

	copy: function() {
		return new InputDictionary(this.required, this.empty);
	}
});

module.exports = InputDictionary;