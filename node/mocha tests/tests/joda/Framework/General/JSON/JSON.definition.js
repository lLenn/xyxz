const { ObjectDefinition, PropertyDefinition } = require("../../../../../helpers/unit");

JSONDefinition = ObjectDefinition.createFrom([
	new PropertyDefinition("id", ["uniqID"]),
    new PropertyDefinition("arr", ["array"]),
    new PropertyDefinition("obj", ["object"]).ofSelf().withDepth(3),
    new PropertyDefinition("str", ["string"]),
    new PropertyDefinition("bool", ["boolean"]),
    new PropertyDefinition("int", ["number"])
]);

module.exports = { JSONDefinition };