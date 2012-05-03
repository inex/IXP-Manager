describe("issue 154", function () {
	beforeEach(function () {
		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			},
			events: {
				click: function () {
					var Wysiwyg = arguments[1];

					// test [this]
					Wysiwyg.issue154 = [this, arguments];
				}
			}
		});
	});

	afterEach(function () {
		$("textarea").wysiwyg("destroy");
	});

	it('should return correct [this] reference to plugin', function () {
		var obj = $("#id1").data("wysiwyg");

		$(obj.editorDoc).trigger("click");

		expect(obj.issue154[0]).toEqual(obj.editorDoc);
		expect(obj.issue154[1][1]).toEqual(obj);
	});
});
