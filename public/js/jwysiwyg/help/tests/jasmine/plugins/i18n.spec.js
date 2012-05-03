describe("[i18n]", function () {
	beforeEach(function () {

	});

	afterEach(function () {
		$("textarea").wysiwyg("destroy");
	});

	it('should set correct lang option', function () {
		var spy = new spyOn($.wysiwyg.i18n, "translateControls");

		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			},
			plugins: {
				i18n: {
					lang: "fr"
				}
			}
		});

		expect(spy.mostRecentCall.args[1]).toEqual("fr");
	});

	it('should run translateControls when language changed with correct lang', function () {
		var spy = new spyOn($.wysiwyg.i18n, "translateControls");

		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			},
			plugins: {
				i18n: {
					lang: "fr"
				}
			}
		});

		$("#id1").wysiwyg("i18n.run", "ru");

		expect($.wysiwyg.i18n.translateControls).toHaveBeenCalled();
		expect(spy.mostRecentCall.args[1]).toEqual("ru");
	});
});
