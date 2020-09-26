const { ObjectDefinition, PropertyDefinition, types } = require("../../../../../helpers/object");
const { ArrayType, ValuesType, StringType, NumberType } = types;
const { JSONDefinition } = require("../../General/JSON/JSON.definition");
const RowDefinition = require("./Row.definition");

TableBaseDefinition = ObjectDefinition.createFrom([
    new PropertyDefinition("dataProvider", [new ArrayType(true, true, JSONDefinition)]),
    new PropertyDefinition("rows", [new ArrayType(true, false, RowDefinition)]),
    new PropertyDefinition("identifier", [new ValuesType(true, ["id"])]),
    new PropertyDefinition("noRecordsPlaceholder", [new StringType(true, false)]),
    new PropertyDefinition("rowHeight", [new NumberType(true, true)]),
]);

module.exports = TableBaseDefinition;