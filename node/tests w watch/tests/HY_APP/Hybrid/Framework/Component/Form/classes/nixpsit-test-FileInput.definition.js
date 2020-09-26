if(typeof require !== "undefined") {
    var { ObjectDefinition, PropertyDefinition } = require("nixpsit-test").testing.UnitTest;
}

(function() {
    FileInputDefinition = ObjectDefinition.createFrom([
        new PropertyDefinition("rights", ["array"]).chooseFrom([["upload", "remove"], [], ["upload"], ["remove"], ["all", "remove"]]),
        new PropertyDefinition("file", ["string"]).isNotRequired()
    ]);
})()

if(typeof module !== "undefined") {
    module.exports = { FileInputDefinition };
}