const types = {
	ArrayType: require("./types/ArrayType.js"),
	BooleanType: require("./types/BooleanType.js"),
	DateType: require("./types/DateType.js"),
	EndOfObject: require("./types/EndOfObject.js"),
	EndOfType: require("./types/EndOfType.js"),
	InputType: require("./types/InputType.js"),
	NumberType: require("./types/NumberType.js"),
	ObjectType: require("./types/ObjectType.js"),
	ParentType: require("./types/ParentType.js"),
	StringType: require("./types/StringType.js"),
	TestType: require("./types/TestType.js"),
	TestDefinition: require("./types/TypeDefinition.js"),
	UniqueType: require("./types/UniqueType.js"),
	ValuesType: require("./types/ValuesType.js")
}
const dictionaries = {
	InputDictionary: require("./dictionaries/InputDictionary"),
	StringDictionary: require("./dictionaries/StringDictionary"),
	TypeDictionary: require("./dictionaries/TypeDictionary"),
}
const filters = {
	DateFilter: require("./filters/DateFilter"),
	Filter: require("./filters/Filter"),
	InputFilter: require("./filters/InputFilter"),
	NumberFilter: require("./filters/NumberFilter"),
	StringFilter: require("./filters/StringFilter"),
}
const TestObject = require("./TestObject.js");
const ObjectDefinition = require("./ObjectDefinition.js");
const FunctionDefinition = require("./FunctionDefinition.js");
const PropertyDefinition = require("./PropertyDefinition.js");

module.exports = { TestObject, ObjectDefinition, PropertyDefinition, FunctionDefinition, types, dictionaries, filters };