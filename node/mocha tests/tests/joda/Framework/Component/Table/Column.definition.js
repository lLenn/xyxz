const { ObjectDefinition, PropertyDefinition, types } = require("../../../../../helpers/object");
const { ParentType, StringType, EndOfType, EndOfObject } = types;
const { JSONDefinition } = require("../../General/JSON/JSON.definition.js");
const NJSON = require("../../../../../JODA/Framework/General/json/js/nixps-json.js");
const { StringRenderer, ArrayRenderer, ObjectRenderer } = require("../../../../../JODA/Framework/Component/Table/js/renderers");
const Column = require("../../../../../JODA/Framework/Component/Table/js/nixps-table-Column.js");

var getDataProvider = function(pParents) {
	var dataProvider;
	for(var i = 0, len = pParents.length; i < len; i++) {
		if(pParents[i]["dataProvider"] !== undefined) {
			return pParents[i]["dataProvider"];
		}
	}
	throw new Error("ColumnDefinition expects to be the child of an object with a 'dataProvider'");
}

ColumnDefinition = ObjectDefinition.createFrom([
	new PropertyDefinition("key", [new ParentType(true, function(pObject, pParents, pMetaData) {
		var dataProvider = getDataProvider(pParents);
		if(dataProvider.length === 0) {
			return new EndOfObject();
		} else {
			var objectKeys = Object.keys(dataProvider[0]);
			if(pMetaData.pointer === objectKeys.length) {
				return new EndOfType();
			} else {
				return objectKeys[pMetaData.pointer];
			}
		}
	}, true)]),
	new PropertyDefinition("label", new StringType(true, false)),
	new PropertyDefinition("cellRenderer", [new ParentType(true, function(pObject, pParents) {
		var dataProvider = getDataProvider(pParents);
		var value = NJSON.getValue(dataProvider[0], pObject.key);
		switch(typeof value) {
			case "number":
			case "boolean":
			case "string": return new StringRenderer();
			case "object":
				if(Array.isArray(value)) {
					return new ArrayRenderer();
				} else if(value !== null) {
					return new ObjectRenderer();
				}
			default: throw new Error("Column definition doesn't recognize type of value: " + value + " for key: " + pObject.key + "!");
		}
	})]),
	new PropertyDefinition("headerRenderer", [new ValuesType(true, [new StringRenderer()])]),
	new PropertyDefinition("cellClass", [new StringType(false, true)]),
	new PropertyDefinition("headerClass", [new StringType(false, true)])
], Column);

module.exports = ColumnDefinition;