describe("plugins", function () {
	beforeEach(function () {
		$("textarea").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			}
		});
	});

	afterEach(function () {
		$("textarea").wysiwyg("destroy");
	});

	it('should return correct [this] reference to plugin', function () {
		var MyPlugin = {
			name: "my_plugin",

			myMethod: function () {
				return this;
			}
		};
		var obj1, obj2;

		$.wysiwyg.plugin.register(MyPlugin);
		
		obj1 = $("#id1").wysiwyg("my_plugin.myMethod");
		obj2 = $.wysiwyg.my_plugin.myMethod();
		
		expect(MyPlugin).toEqual(obj1);
		expect(MyPlugin).toEqual(obj2);
	});
});
