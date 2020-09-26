var NJSON = require("../../../../../JODA/Framework/General/JSON/js/nixps-JSON.js");
var QueryApplier = require("../../../../../cloudflow/CloudflowUtil/js/QueryApplier.js");


describe("NJSON", function() {
	var json = {
		string: "string",
		number: 123,
		arr: [
			{ id: "1", number: 123	},
			{ id: "2", number: 123	},
			{ id: "3", number: 123	}
		],
		obj: {
			string: "string",
			number: 123,
			arr: [
				{ id: "4", number: 123	},
				{ id: "5", number: 123	},
				{ id: "6", number: 123	}
			],
			obj: {
				string: "string",
				number: 123,
				arr: [
					{ id: "7", number: 123	},
					{ id: "8", number: 123	},
					{ id: "9", number: 123	}
				]
			}
		},
		nestedArr:  [
			{
				id: "0",
				string: "string",
				number: 123,
				arr: [
					{ id: "10", number: 123	},
					{ id: "11", number: 123	},
					{ id: "12", number: 123	}
				],
				obj: {
					string: "string",
					number: 123,
					arr: [
						{ id: "13", number: 123	},
						{ id: "14", number: 123	},
						{ id: "15", number: 123	}
					]
				}
			},
			{ id: "16", number: 123	},
			{ id: "17", number: 123	},
			{ id: "18", number: 123	}
		],
		twoDArr: [
			[
				{ id: "19", number: 123	},
				{ id: "20", number: 123	},
				{ id: "21", number: 123	}
			],[
				{ id: "22", number: 123	},
				{ id: "23", number: 123	},
				{ id: "24", number: 123	}
			],[
				{ id: "25", number: 123	},
				{ id: "26", number: 123	},
				{ id: "27", number: 123	}
			]
		],
		extremelyNestedArr: [
			[
				{ id: "27", number: 123	},
				{ id: "28", number: 123	},
				{ id: "29", number: 123	}
			],[
				[
					[
						{ id: "30", number: 123	},
						{ id: "31", number: 123	},
						{ id: "32", number: 123	}
					],[
						{ id: "33", number: 123	},
						{ id: "34", number: 123	},
						{ id: "35", number: 123	}
					],[
						[
							{ id: "36", number: 123	},
							{ id: "37", number: 123	},
							{ id: "38", number: 123	}
						],[
							[
								{ id: "39", number: 123	},
								{ id: "40", number: 123	},
								{ id: "41", number: 123	}
							],[
								[
									{ id: "42", number: 123	},
									{ id: "43", number: 123	},
									{ id: "44", number: 123	}
								],[
									{ id: "45", number: 123	},
									{ id: "46", number: 123	},
									{ id: "47", number: 123	}
								],[
									{ id: "48", number: 234	},
									{ id: "49", number: 123	},
									{ id: "50", number: 123	}
								]
							],[
								{ id: "51", number: 123	},
								{ id: "52", number: 123	},
								{ id: "53", number: 123	}
							]
						],[
							{ id: "54", number: 123	},
							{ id: "55", number: 123	},
							{ id: "56", number: 123	}
						]
					]
				],[
					{ id: "57", number: 123	},
					{ id: "58", number: 123	},
					{ id: "59", number: 123	}
				],[
					{ id: "60", number: 123	},
					{ id: "61", number: 123	},
					{ id: "62", number: 123	}
				]
			],[
				{ id: "63", number: 123	},
				{ id: "64", number: 123	},
				{ id: "65", number: 123	}
			]
		]
	}

	describe("bindJSON", function() {
		it("should add a value where there's a string like {data-key|...}");
		it("should add a translation where there's a string like {localization|...}");
	});

	describe("_getSecondToLastValue", function() {
		it("should split the path given", function() {
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "string").subKeys).to.have.ordered.members(["string"]);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "arr.1").subKeys).to.have.ordered.members(["arr", "1"]);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "nestedArr.0.arr.1").subKeys).to.have.ordered.members(["nestedArr", "0", "arr", "1"]);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "twoDArr.1.1").subKeys).to.have.ordered.members(["twoDArr", "1", "1"]);
			var compareConditions = [
				{ key: "extremelyNestedArr.1.0.2.1.1.2", func: function(pObject) {
					return pObject.id === "48";
				}}
			];
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "extremelyNestedArr.1.0.2.1.1.2.number", compareConditions).subKeys).to.have.ordered.members(["extremelyNestedArr", "1", "0", "2", "1", "1", "2", "number"]);
		});
		it("should return undefined when a path containing no arrays can't be resolved, excluding the last key", function() {
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "err.something")).to.be.undefined;
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "obj.obj.err.something")).to.be.undefined;
		});
		it("should return undefined when a path containing arrays with indices can't be resolved, excluding the last key", function() {
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "nestedArr.1.arr.1")).to.be.undefined;
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "arr.4.something")).to.be.undefined;
		});
		it("should return undefined when a path containing arrays with compareConditions can't be resolved, excluding the last key", function() {
			var compareConditions = [
				{ key: "arr", func: function(pObject) {
					return pObject.id === "3";
				}},
				{ key: "twoDArr.0", func: function(pObject) {
					return pObject.id === "23";
				}}
			];
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "nestedArr.something", compareConditions)).to.be.undefined;
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "twoDArr.0.something")).to.be.undefined;
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "twoDArr.0.something", compareConditions)).to.be.undefined;
		});

		it("should retrieve the second of last element of an existing path", function() {
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "string").subObject).to.deep.equal(json);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "arr.1").subObject).to.deep.equal([
				{ id: "1", number: 123	},
				{ id: "2", number: 123	},
				{ id: "3", number: 123	}
			]);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "nestedArr.0.arr.1").subObject).to.deep.equal([
				{ id: "10", number: 123	},
				{ id: "11", number: 123	},
				{ id: "12", number: 123	}
			]);
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "twoDArr.1.1").subObject).to.deep.equal([
				{ id: "22", number: 123	},
				{ id: "23", number: 123	},
				{ id: "24", number: 123	}
			]);
			var compareConditions = [
				{ key: "twoDArr.0", func: function(pObject) {
					return pObject.id === "19";
				}}
			];
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "twoDArr.0.number", compareConditions).subObject).to.deep.equal({ id: "19", number: 123	});
			var compareConditions = [
				{ key: "extremelyNestedArr.1.0.2.1.1.2", func: function(pObject) {
					return pObject.id === "48";
				}}
			];
			expect(NJSON._getSecondToLastValue($.extend(true, {}, json), "extremelyNestedArr.1.0.2.1.1.2.number", compareConditions).subObject).to.deep.equal({ id: "48", number: 234 });
		});
	});

	describe("setValue", function() {
		it("should return undefined when _getSecondToLastValue return undefined", function() {
			expect(NJSON.setValue($.extend(true, {}, json), "err.something"), 234).to.be.undefined;
			expect(NJSON.setValue($.extend(true, {}, json), "obj.obj.err.something"), 234).to.be.undefined;
		});

		it("should add a property when it doesn't exist", function() {
			expect(NJSON.setValue($.extend(true, {}, json), ".", 234)).to.equal(234);
			expect(NJSON.setValue($.extend(true, {}, json), "unknown", 234).unknown).to.equal(234);
			expect(NJSON.setValue($.extend(true, {}, json), "obj.unknown", 234).obj.unknown).to.equal(234);
			expect(NJSON.setValue($.extend(true, {}, json), "obj.obj.unknown", 234).obj.obj.unknown).to.equal(234);

			expect(NJSON.setValue($.extend(true, {}, json), "unknown", 234, undefined, true).unknown).to.equal(234);
			expect(NJSON.setValue($.extend(true, {}, json), "obj.unknown", 234, undefined, true).obj.unknown).to.equal(234);
			expect(NJSON.setValue($.extend(true, {}, json), "obj.obj.unknown", 234, undefined, true).obj.obj.unknown).to.equal(234);

			var compareConditions = [
				{ key: "obj.obj.newarr", func: function(pObject) {
					return pObject.id === "4";
				}}
			];
			expect(NJSON.setValue($.extend(true, {}, json), "obj.obj.newarr", { id: "4", number: 234 }, compareConditions).obj.obj.newarr[0]).to.deep.equal({ id: "4", number: 234 });
			expect(NJSON.setValue($.extend(true, {}, json), "obj.obj.newarr", { id: "4", number: 234 }, compareConditions, true).obj.obj.newarr[0]).to.deep.equal({ id: "4", number: 234 });
		});

		it("should replace the element in an array when path ends with an index", function() {
			expect(NJSON.setValue($.extend(true, {}, json), "arr.2", { id: "3", number: 234 }).arr[2]).to.deep.equal({ id: "3", number: 234 });
			expect(NJSON.setValue($.extend(true, {}, json), "arr.2", { number: 234 }, undefined, true).arr[2]).to.deep.equal({ id: "3", number: 234 });
		});

		it("should replace the element in an array when the search condition is met", function() {
			var compareConditions = [
				{ key: "arr", func: function(pObject) {
					return pObject.id === "3";
				}}
			];
			expect(NJSON.setValue($.extend(true, {}, json), "arr", { id: "3", number: 234 }, compareConditions).arr[2]).to.deep.equal({ id: "3", number: 234 });
			expect(NJSON.setValue($.extend(true, {}, json), "arr", { number: 234 }, compareConditions, true).arr[2]).to.deep.equal({ id: "3", number: 234 });
		});

		it("should push to an array when the search condition isn't met", function() {
			var compareConditions = [
				{ key: "arr", func: function(pObject) {
					return pObject.id === "4";
				}}
			];
			expect(NJSON.setValue($.extend(true, {}, json), "arr", { id: "4", number: 234 }, compareConditions).arr[3]).to.deep.equal({ id: "4", number: 234 });
			expect(NJSON.setValue($.extend(true, {}, json), "arr", { id: "4", number: 234 }, compareConditions, true).arr[3]).to.deep.equal({ id: "4", number: 234 });
		});

		it("should remove the element when the value is undefined", function(){
			var arr = NJSON.setValue($.extend(true, {}, json), "arr.1", undefined).arr;
			expect(arr[1]).to.deep.equal({ id: "3", number: 123 });
			expect(arr).has.length(2);

			expect(NJSON.setValue($.extend(true, {}, json), "arr.1.number", undefined).arr[1]).to.deep.equal({ id: "2" });

			var compareConditions = [
				{ key: "arr", func: function(pObject) {
					return pObject.id === "4";
				}}
			];
			arr = NJSON.setValue($.extend(true, {}, json), "arr", undefined, compareConditions).arr;
			expect(arr).has.length(3);

			var compareConditions = [
				{ key: "arr", func: function(pObject) {
					return pObject.id === "1";
				}}
			];
			arr = NJSON.setValue($.extend(true, {}, json), "arr", undefined, compareConditions).arr;
			expect(arr).to.deep.equal([
				{ id: "2", number: 123	},
				{ id: "3", number: 123	}
			]);

			compareConditions = [
				{ key: "obj.obj.newarr", func: function(pObject) {
					return pObject.id === "4";
				}}
			];
			var obj = NJSON.setValue($.extend(true, {}, json), "obj.obj.newarr", undefined, compareConditions).obj.obj;
			expect(obj).to.deep.equal({
				string: "string",
				number: 123,
				arr: [
					{ id: "7", number: 123	},
					{ id: "8", number: 123	},
					{ id: "9", number: 123	}
				]
			});
		});
	});

	describe("getValue", function() {
		it("should return undefined when _getSecondToLastValue return undefined", function() {
			expect(NJSON.getValue($.extend(true, {}, json), "err.something")).to.be.undefined;
			expect(NJSON.getValue($.extend(true, {}, json), "obj.obj.err.something")).to.be.undefined;
		});

		it("should return undefined when a element doesn't exist", function() {
			expect(NJSON.getValue($.extend(true, {}, json), "unknown")).to.be.undefined;
			expect(NJSON.getValue($.extend(true, {}, json), "obj.unknown")).to.be.undefined;
			expect(NJSON.getValue($.extend(true, {}, json), "obj.obj.unknown")).to.be.undefined;
		});

		it("should return the element at a path", function() {
			expect(NJSON.getValue($.extend(true, {}, json), ".")).to.deep.equal(json);
			expect(NJSON.getValue($.extend(true, {}, json), "arr.2")).to.deep.equal({ id: "3", number: 123 });
			expect(NJSON.getValue($.extend(true, {}, json), "obj.obj.string")).to.equal("string");
			expect(NJSON.getValue($.extend(true, {}, json), "twoDArr")).to.deep.equal(json.twoDArr);
			var compareConditions = [
				{ key: "extremelyNestedArr.1.0.2.1.1.2", func: function(pObject) {
					return pObject.id === "48";
				}}
			];
			expect(NJSON.getValue($.extend(true, {}, json), "extremelyNestedArr.1.0.2.1.1.2.number", compareConditions)).to.equal(234);
		});
	});
});