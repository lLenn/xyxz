const { ObjectDefinition, PropertyDefinition, types } = require("../../../../../helpers/object");
const { ArrayType } = types;
const ColumnDefinition = require("./Column.definition");
const Row = require("../../../../../JODA/Framework/Component/Table/js/nixps-table-Row.js");

RowDefinition = ObjectDefinition.createFrom([
    new PropertyDefinition("columns", [new ArrayType(true, false, ColumnDefinition)])
], Row);

module.exports = RowDefinition;