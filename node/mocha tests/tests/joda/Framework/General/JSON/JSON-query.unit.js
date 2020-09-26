const NJSON = require("../../../../../JODA/Framework/General/JSON/js/nixps-JSON.js");
const QueryApplier = require("../../../../../cloudflow/CloudflowUtil/js/QueryApplier.js");
const { JSONDefinition } = require("./JSON.definition.js");

describe("NJSON.query vs QueryApplier", function() {
	this.slow(300);

	describe("should fail", function () {
		it("wrong identifier", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};
			(function(){
				(new QueryApplier(["name", "unknown_wrong_identifier to", "piet"])).validate(testobject);
			}).should.throw();
			(function(){
				NJSON.query([testobject], ["name", "unknown_wrong_identifier to", "piet"]);
			}).should.throw();
		});
	});

	describe("basic validate", function () {
		it("equal to", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "equal to", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["age", "equal to", 98])).validate(testobject).should.eql(true);
			(new QueryApplier(["boss", "equal to", true])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "equal to", "jan"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "equal to", "pietF"])).validate(testobject).should.eql(false);
			(new QueryApplier(["age", "equal to", 980])).validate(testobject).should.eql(false);
			(new QueryApplier(["boss", "equal to", false])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "equal to", "janF"])).validate(testobject).should.eql(false);

			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["employs.0.name", "equal to", "jan"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query([testobject], ["name", "equal to", "piet"])).should.have.length(1);
			(NJSON.query([testobject], ["age", "equal to", 98])).should.have.length(1);
			(NJSON.query([testobject], ["boss", "equal to", true])).should.have.length(1);
			(NJSON.query([testobject], ["employs.0.name", "equal to", "jan"])).should.have.length(1);

			(NJSON.query([testobject], ["name", "equal to", "pietF"])).should.have.length(0);
			(NJSON.query([testobject], ["age", "equal to", 980])).should.have.length(0);
			(NJSON.query([testobject], ["boss", "equal to", false])).should.have.length(0);
			(NJSON.query([testobject], ["employs.0.name", "equal to", "janF"])).should.have.length(0);

			for(var i = 0; i < 1000; i++) {
				(NJSON.query([testobject], ["employs.0.name", "equal to", "jan"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("equal to (include missings)", function () {
			var testobjectA = {
				"boss": true
			};
			var testobjectB = {
				"boss": false
			};
			var testobjectC = {
				"name": "anne"
			};

			var time = Date.now();
			(new QueryApplier(["boss", "equal to", true])).validate(testobjectA).should.eql(true);
			(new QueryApplier(["boss", "equal to", true])).validate(testobjectB).should.eql(false);
			(new QueryApplier(["boss", "equal to", true])).validate(testobjectC).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["boss", "equal to", true])).validate(testobjectA).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query([testobjectA], ["boss", "equal to", true])).should.have.length(1);
			(NJSON.query([testobjectB], ["boss", "equal to", true])).should.have.length(0);
			(NJSON.query([testobjectC], ["boss", "equal to", true])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query([testobjectA], ["boss", "equal to", true])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("not equal to", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["boss", "not equal to", false])).validate(testobject).should.eql(true);
			(new QueryApplier(["boss1", "not equal to", false])).validate(testobject).should.eql(true);
			(new QueryApplier(["boss", "not equal to", true])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["boss", "not equal to", false])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["boss", "not equal to", false])).should.have.length(1);
			(NJSON.query(testobject, ["boss1", "not equal to", false])).should.have.length(1);
			(NJSON.query(testobject, ["boss", "not equal to", true])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["boss", "not equal to", false])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("exists", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "exists"])).validate(testobject).should.eql(true);
			(new QueryApplier(["fack_name", "exists"])).validate(testobject).should.eql(false);
			(new QueryApplier(["age.test", "exists"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "exists"])).validate(testobject).should.eql(true);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "exists"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "exists"])).should.have.length(1);
			(NJSON.query(testobject, ["fack_name", "exists"])).should.have.length(0);
			(NJSON.query(testobject, ["age.test", "exists"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "exists"])).should.have.length(1);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "exists"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("begins with", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "begins with", "pie"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "begins with", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "begins with", "j"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "begins with", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "begins with", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "begins with", "pie"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "begins with", "pie"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "begins with", "piet"])).should.have.length(1);
			(NJSON.query(testobject, ["employs.0.name", "begins with", "j"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "begins with", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "begins with", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "begins with", "pie"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("begins like", function () {
			var testobject = {
				"name": "Piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "Jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "begins like", "pie"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "begins like", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "begins like", "j"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "begins like", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "begins like", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "begins like", "pie"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "begins like", "pie"])).should.have.length(1);
			(NJSON.query(testobject,["name", "begins like", "piet"])).should.have.length(1);
			(NJSON.query(testobject,["employs.0.name", "begins like", "j"])).should.have.length(1);

			(NJSON.query(testobject, ["name", "begins like", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "begins like", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "begins like", "pie"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("ends with", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "ends with", "et"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "ends with", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "ends with", "n"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "ends with", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "ends with", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "ends with", "et"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "ends with", "et"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "ends with", "piet"])).should.have.length(1);
			(NJSON.query(testobject, ["employs.0.name", "ends with", "n"])).should.have.length(1);

			(NJSON.query(testobject, ["name", "ends with", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "ends with", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "ends with", "et"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("ends like", function () {
			var testobject = {
				"name": "Piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "Jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "ends like", "et"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "ends like", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "ends like", "n"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "ends like", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "ends like", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "ends like", "et"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "ends like", "et"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "ends like", "piet"])).should.have.length(1);
			(NJSON.query(testobject, ["employs.0.name", "ends like", "n"])).should.have.length(1);

			(NJSON.query(testobject, ["name", "ends like", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "ends like", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "ends like", "et"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("contains", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "contains", "ie"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "contains", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "contains", "a"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "contains", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "contains", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "contains", "ie"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "contains", "ie"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "contains", "piet"])).should.have.length(1);
			(NJSON.query(testobject, ["employs.0.name", "contains", "a"])).should.have.length(1);

			(NJSON.query(testobject, ["name", "contains", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "contains", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "contains", "ie"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("contains text like", function () {
			var testobject = {
				"name": "Piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "Jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "contains text like", "ie"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "contains text like", "piet"])).validate(testobject).should.eql(true);
			(new QueryApplier(["employs.0.name", "contains", "a"])).validate(testobject).should.eql(true);

			(new QueryApplier(["name", "contains text like", "fa"])).validate(testobject).should.eql(false);
			(new QueryApplier(["employs.0.name", "contains text like", "fa"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "contains text like", "ie"])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "contains text like", "ie"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "contains text like", "piet"])).should.have.length(1);
			(NJSON.query(testobject, ["employs.0.name", "contains", "a"])).should.have.length(1);

			(NJSON.query(testobject, ["name", "contains text like", "fa"])).should.have.length(0);
			(NJSON.query(testobject, ["employs.0.name", "contains text like", "fa"])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "contains text like", "ie"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("less than", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["age", "less than", 100])).validate(testobject).should.eql(true);
			(new QueryApplier(["age", "less than", 98])).validate(testobject).should.eql(false);
			(new QueryApplier(["age", "less than", 50])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["age", "less than", 100])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["age", "less than", 100])).should.have.length(1);
			(NJSON.query(testobject, ["age", "less than", 98])).should.have.length(0);
			(NJSON.query(testobject, ["age", "less than", 50])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["age", "less than", 100])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("less than or equal to", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["age", "less than or equal to", 98])).validate(testobject).should.eql(true);
			(new QueryApplier(["age", "less than or equal to", 50])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["age", "less than or equal to", 98])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["age", "less than or equal to", 98])).should.have.length(1);
			(NJSON.query(testobject, ["age", "less than or equal to", 50])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["age", "less than or equal to", 98])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("greater than", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["age", "greater than", 50])).validate(testobject).should.eql(true);
			(new QueryApplier(["age", "greater than", 98])).validate(testobject).should.eql(false);
			(new QueryApplier(["age", "greater than", 100])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["age", "greater than", 50])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["age", "greater than", 50])).should.have.length(1);
			(NJSON.query(testobject, ["age", "greater than", 98])).should.have.length(0);
			(NJSON.query(testobject, ["age", "greater than", 100])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["age", "greater than", 50])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("greater than or equal to", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["age", "greater than or equal to", 50])).validate(testobject).should.eql(true);
			(new QueryApplier(["age", "greater than or equal to", 110])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["age", "greater than or equal to", 50])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["age", "greater than or equal to", 50])).should.have.length(1);
			(NJSON.query(testobject, ["age", "greater than or equal to", 110])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["age", "greater than or equal to", 50])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("in", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "in", ["an", "piet", "piraat"]])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "in", ["an", "pieter", "piraat"]])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "in", ["an", "piet", "piraat"]])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "in", ["an", "piet", "piraat"]])).should.have.length(1);
			(NJSON.query(testobject, ["name", "in", ["an", "pieter", "piraat"]])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "in", ["an", "piet", "piraat"]])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("not", function() {
			this.timeout(10000);

			var testObject = new TestObject(new JSONDefinition());
			var objects = [];
			var not1ToBeFound = [];
			var not2ToBeFound = [];
			var not3ToBeFound = [];
			for(var i = 0; i < 300; i++) {
				var addToAll = false;
				var object = testObject.createRandomObject();
				objects.push(object);
				if(object.str.indexOf("a") === -1 || (object.obj !== undefined && object.obj.obj !== undefined && object.obj.obj.bool === true)) {
					not1ToBeFound.push(object);
				}

				if(object.str.indexOf("a") !== -1 || (object.obj !== undefined && object.obj.obj !== undefined && object.obj.obj.bool === false)) {
					not2ToBeFound.push(object);
				}

				if(object.str.indexOf("a") === -1 || (object.obj !== undefined && object.obj.obj !== undefined && object.obj.obj.bool === false)) {
					not3ToBeFound.push(object);
				}
			}

			var found = NJSON.query(objects, ["(", "str", "contains text like", "A", "not", "or", "obj.obj.bool", "equal to", true, ")"]);
			expect(found.length).to.equal(not1ToBeFound.length);
			expect(found).to.have.deep.members(not1ToBeFound);

			found = NJSON.query(objects, ["(", "(", "str", "contains text like", "A", ")", "not", "or", "obj.obj.bool", "equal to", true, ")"]);
			expect(found.length).to.equal(not1ToBeFound.length);
			expect(found).to.have.deep.members(not1ToBeFound);

			found = NJSON.query(objects, ["(", "(", "str", "contains text like", "A", "not", ")", "or", "obj.obj.bool", "equal to", true, ")"]);
			expect(found.length).to.equal(not1ToBeFound.length);
			expect(found).to.have.deep.members(not1ToBeFound);

			found = NJSON.query(objects, ["str", "contains text like", "A", "or", "not", "obj.obj.bool", "equal to", true]);
			expect(found.length).to.equal(not2ToBeFound.length);
			expect(found).to.have.deep.members(not2ToBeFound);

			found = NJSON.query(objects, ["(", "str", "contains text like", "A", "or", "not", "(", "obj.obj.bool", "equal to", true, ")", ")"]);
			expect(found.length).to.equal(not2ToBeFound.length);
			expect(found).to.have.deep.members(not2ToBeFound);

			found = NJSON.query(objects, ["str", "contains text like", "A", "or", "(", "not", "obj.obj.bool", "equal to", true, ")"]);
			expect(found.length).to.equal(not2ToBeFound.length);
			expect(found).to.have.deep.members(not2ToBeFound);

			found = NJSON.query(objects, ["str", "contains text like", "A", "not", "or", "not", "obj.obj.bool", "equal to", true]);
			expect(found.length).to.equal(not3ToBeFound.length);
			expect(found).to.have.deep.members(not3ToBeFound);

			found = NJSON.query(objects, ["(", "not", "(", "str", "contains text like", "A", ")", "or", "not", "(", "obj.obj.bool", "equal to", true, ")", ")"]);
			expect(found.length).to.equal(not3ToBeFound.length);
			expect(found).to.have.deep.members(not3ToBeFound);

			found = NJSON.query(objects, ["not", "(", "str", "contains text like", "A", "and", "obj.obj.bool", "equal to", true, ")"]);
			expect(found.length).to.equal(not3ToBeFound.length);
			expect(found).to.have.deep.members(not3ToBeFound);
		});

		it("contains element matching", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{ name: "jan", age: 45, boss: false },
					{ name: "jansens",age: 46, boss: false }
				]
			};

			/*
			 * Not implemented by QueryApplier
			var time = Date.now();
			(new QueryApplier(["employs", "contains element matching", "(", "age", "equal to", 45, ")"])).validate(testobject).should.eql(true)
			(new QueryApplier(["employs", "contains element matching", "(", "age", "equal to", 47, ")"])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["employs", "contains element matching", "(", "name", "equal to", "jan", ")"])).validate(testobject).should.eql(true)
			}
			var queryApplierPerformance = Date.now() - time;
			 */

			//time = Date.now();
			(NJSON.query(testobject, ["employs", "contains element matching", "(", "age", "equal to", 45, ")"])).should.have.length(1);
			(NJSON.query(testobject, ["employs", "contains element matching", "(", "age", "equal to", 47, ")"])).should.have.length(0);
			(NJSON.query(testobject, ["employs", "contains element matching", "(", "name", "equal to", "jan", ")"])).should.have.length(1);
			(NJSON.query(testobject, ["employs", "contains element matching", "(", "name", "contains", "janf", ")"])).should.have.length(0);
			(NJSON.query(testobject, ["employs", "contains element matching", "(", "name", "contains", "jans", ")"])).should.have.length(1);
			/*
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["employs", "contains element matching", "(", "name", "equal to", "jan", ")"])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;
			 */

			//(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});
	});

	describe("combinations validate", function () {
		it("equal to", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "equal to", "piet", "and", "age", "equal to", 98])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "equal to", "piet", "or", "age", "equal to", 98])).validate(testobject).should.eql(true);
			(new QueryApplier(["(", "(", "name", "equal to", "piet", ")", "or", "age", "equal to", 98, ")"])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "equal to", "wrong", "or", "age", "equal to", 98])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "equal to", "wrong", "or", "age", "equal to", 666])).validate(testobject).should.eql(false);
			(new QueryApplier(["name", "equal to", "wrong", "and", "age", "equal to", 666])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "equal to", "piet", "and", "age", "equal to", 98])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "equal to", "piet", "and", "age", "equal to", 98])).should.have.length(1);
			(NJSON.query(testobject, ["name", "equal to", "piet", "or", "age", "equal to", 98])).should.have.length(1);
			(NJSON.query(testobject, ["(", "(", "name", "equal to", "piet", ")", "or", "age", "equal to", 98, ")"])).should.have.length(1);
			(NJSON.query(testobject, ["name", "equal to", "wrong", "or", "age", "equal to", 98])).should.have.length(1);
			(NJSON.query(testobject, ["name", "equal to", "wrong", "or", "age", "equal to", 666])).should.have.length(0);
			(NJSON.query(testobject, ["name", "equal to", "wrong", "and", "age", "equal to", 666])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "equal to", "piet", "and", "age", "equal to", 98])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("equal to and in", function () {
			var testobject = {
				"name": "piet",
				"age": 98,
				"boss": true,
				"employs": [
					{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
					]
			};

			var time = Date.now();
			(new QueryApplier(["name", "equal to", "piet", "and", "age", "in", [1, 12, 98, 20]])).validate(testobject).should.eql(true);
			(new QueryApplier(["name", "equal to", "piet", "and", "age", "in", [1, 12, 980, 20]])).validate(testobject).should.eql(false);
			for(var i = 0; i < 1000; i++) {
				(new QueryApplier(["name", "equal to", "piet", "and", "age", "in", [1, 12, 98, 20]])).validate(testobject).should.eql(true);
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			(NJSON.query(testobject, ["name", "equal to", "piet", "and", "age", "in", [1, 12, 98, 20]])).should.have.length(1);
			(NJSON.query(testobject, ["name", "equal to", "piet", "and", "age", "in", [1, 12, 980, 20]])).should.have.length(0);
			for(var i = 0; i < 1000; i++) {
				(NJSON.query(testobject, ["name", "equal to", "piet", "and", "age", "in", [1, 12, 98, 20]])).should.have.length(1);
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

	});

	describe("apply", function(){
		it("basic apply", function(){
			var testobjects = [
				{
					"name": "piet",
					"age": 98,
					"boss": true,
					"employs": [
						{name: "piet", age: 45, boss: false}, {name: "piets",age: 46, boss: false}
						]
				},
				{
					"name": "jan",
					"age": 8,
					"boss": false,
					"employs": [
						{name: "jan", age: 45, boss: false}, {name: "jansens",age: 46, boss: false}
						]
				},
				{
					"name": "anne",
					"age": 28,
					"boss": false,
					"employs": [
						{name: "anne", age: 45, boss: false}, {name: "annes",age: 46, boss: false}
						]
				},
				];

			var time = Date.now();
			for(var i = 0; i < 1000; i++) {
				var AdultApplier = new QueryApplier(["age", "greater than or equal to", 18]);
				var results = AdultApplier.apply(testobjects);
				$.isArray(results).should.eql(true);
				results.length.should.eql(2);
				results[0].name.should.eql("piet");
				results[1].name.should.eql("anne");
			}
			var queryApplierPerformance = Date.now() - time;


			time = Date.now();
			for(var i = 0; i < 1000; i++) {
				var results = NJSON.query(testobjects, ["age", "greater than or equal to", 18]);
				$.isArray(results).should.eql(true);
				results.length.should.eql(2);
				results[0].name.should.eql("piet");
				results[1].name.should.eql("anne");
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("combinations apply", function () {
			var testobjects = [
				{
					"name": "piet",
					"age": 98,
					"boss": true,
					"employs": [
						{name: "piet", age: 45, boss: false}, {name: "piets", age: 46, boss: false}
						]
				},
				{
					"name": "jan",
					"age": 8,
					"boss": false,
					"employs": [
						{name: "jan", age: 45, boss: false}, {name: "jansens", age: 46, boss: false}
						]
				},
				{
					"name": "anne",
					"age": 28,
					"boss": false,
					"employs": [
						{name: "anne", age: 45, boss: false}, {name: "annes", age: 46, boss: false}
						]
				},
				];
			var time = Date.now();
			for(var i = 0; i < 1000; i++) {
				var AdultApplier = new QueryApplier(["(", "age", "greater than or equal to", 18, "or", "(", "name", "begins with", "pi", ")", ")"]);
				var results = AdultApplier.apply(testobjects);
				$.isArray(results).should.eql(true);
				results.length.should.eql(2);
				results[0].name.should.eql("piet");
				results[1].name.should.eql("anne");
			}
			var queryApplierPerformance = Date.now() - time;

			time = Date.now();
			for(var i = 0; i < 1000; i++) {
				var results = NJSON.query(testobjects, ["(", "age", "greater than or equal to", 18, "or", "(", "name", "begins with", "pi", ")", ")"]);
				$.isArray(results).should.eql(true);
				results.length.should.eql(2);
				results[0].name.should.eql("piet");
				results[1].name.should.eql("anne");
			}
			var njsonQueryPerformance = Date.now() - time;

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		});

		it("should do complicated queries", function() {
			var testObject = new TestObject(new JSONDefinition());
			var objects = [];
			var matchToBeFound = [];
			var boolToBeFound = [];
			var andOrToBeFound = [];
			var allToBeFound = [];
			for(var i = 0; i < 300; i++) {
				var addToAll = false;
				var object = testObject.createRandomObject();
				objects.push(object);
				if(object.obj !== undefined && object.obj.obj !== undefined && object.obj.obj.obj !== undefined && object.obj.obj.obj.str.indexOf("sum") !== -1) {
					matchToBeFound.push(object);
					addToAll = true;
				}
				if(object.obj !== undefined && object.obj !== undefined && object.obj.bool === true) {
					boolToBeFound.push(object);
					addToAll = true;
				}
				if((object.str.indexOf("sum") !== -1 && ((object.obj !== undefined && object.obj.int <= 0) || (object.obj !== undefined && object.obj.obj.bool === false))) || object.arr[0] === "30") {
					andOrToBeFound.push(object);
				}
				if(addToAll) {
					allToBeFound.push(object);
				}
			}

			var foundElements = NJSON.query(objects, ["obj.bool", "equal to", "true", "or", "obj.obj", "contains element matching", "(", "obj", "contains element matching", "(", "str", "contains", "sum", ")", ")"]);
			expect(foundElements.length).to.equal(allToBeFound.length);
			expect(foundElements).to.have.deep.members(allToBeFound);

			var time = Date.now();
			for(var i = 0; i < 10; i++) {
				foundElements = new QueryApplier(["(", "str", "contains", "sum", "and", "(", "obj.int", "less than or equal to", 0, "or", "obj.obj.bool", "equal to", false, ")", ")", "or", "arr.0", "equal to", "30"]).apply(objects);
			}
			var queryApplierPerformance = Date.now() - time;
			expect(foundElements.length).to.equal(andOrToBeFound.length);
			expect(foundElements).to.have.deep.members(andOrToBeFound);

			time = Date.now();
			for(var i = 0; i < 10; i++) {
				foundElements = NJSON.query(objects, ["(", "str", "contains", "sum", "and", "(", "obj.int", "less than or equal to", 0, "or", "obj.obj.bool", "equal to", false, ")", ")", "or", "arr.0", "equal to", "30"]);
			}
			var njsonQueryPerformance = Date.now() - time;
			expect(foundElements.length).to.equal(andOrToBeFound.length);
			expect(foundElements).to.have.deep.members(andOrToBeFound);

			(njsonQueryPerformance < queryApplierPerformance).should.be.true;
		})
	});
});