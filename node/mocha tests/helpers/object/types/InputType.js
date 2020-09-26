const TypeDefinition = require("./TypeDefinition.js");
const  EndOfType = require("./EndOfType.js");
const InputDictionary = require("../dictionaries/InputDictionary.js");

InputType = function(pRequired, pFilter, pDictionary) {
	this.typeDefinition(pRequired, pFilter);

	if(pDictionary === undefined) {
		pDictionary = new InputDictionary();
	}

	this.dictionary = pDictionary;
}

InputType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: InputType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		var form = {
			contents: {
				elements: []
			},
			layout: {
				type: "bootstrapGrid",
				parameters: []
			}
		}
		switch(pPointer) {
			case 0: if(this.empty === true) { return form; } else { this.pointer++; };
			case 1:
				form.contents.elements.push(this.dictionary.getField());
				form.layout.parameters.push(this.form.contents.elements[0].element.component.id);
				form.contents.elements.push(this.dictionary.getComponent());
				form.layout.parameters.push(this.form.contents.elements[1].element.id);
				break;
			case 2:
				form.contents.elements.push(this.dictionary.getField());
				form.layout.parameters.push(this.form.contents.elements[0].element.component.id);
				break;
			case 3:
				contents.elements.push(this.dictionary.getComponent());
				form.layout.parameters.push(this.form.contents.elements[0].element.id);
				contents.elements.push(this.dictionary.getComponent());
				form.layout.parameters.push(this.form.contents.elements[1].element.id);
				contents.elements.push(this.dictionary.getField());
				form.layout.parameters.push(this.form.contents.elements[1].element.component.id);
				break;
			default: return new EndOfType();
		}
		return contents;
	},

	copy: function() {
		return new InputType(this.required, this.filters.map(function(pFilter) { return pFilter.copy(); }), this.dictionary.copy());
	}
});

module.exports = InputType;